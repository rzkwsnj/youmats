<?php

namespace App\Http\Controllers\Front\Vendor\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\CancelSubscribe;
use App\Http\Requests\Vendor\SubScribeRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Membership;
use App\Models\Subscribe;
use App\Notifications\VendorSubscribeCanceled;
use App\Notifications\VendorSubscribed;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use zakariatlilani\payfort\Facades\Payment;

class SubScribeController extends Controller
{
    public string $provider = 'payfort';

    public function __construct()
    {
        $this->middleware('auth:vendor');
        //If you would like to add "vendor verification middleware":
        $this->middleware('verified:vendor.verification.notice');
    }

    public function index()
    {
        $data['vendor'] = Auth::guard('vendor')->user();

        $data['categories'] = Category::join('products', 'products.category_id', '=', 'categories.id')
            ->join('vendors', 'vendors.id', '=', 'products.vendor_id')
            ->join('categories_memberships', 'categories_memberships.category_id', '=', 'categories.id')
            ->join('memberships', 'categories_memberships.membership_id', '=', 'memberships.id')
            ->where('vendors.id', $data['vendor']->id)
            ->where('memberships.status', true)
            ->select('categories.*')
            ->distinct()->get();

        //        dd($data);

        $data['current_subscribes'] = $data['vendor']->current_subscribes;

        return view('vendorAdmin.subscribe.index')->with($data);
    }

    public function upgrade(SubScribeRequest $request)
    {
        $data = $request->validated();
        //        try {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['membership'] = Membership::findorfail($data['membership_id']);
        $data['category'] = Category::findorfail($data['category_id']);

        if (isSubscribe($data['vendor']->current_subscribes, $data['category_id'], $data['membership_id']))
            return abort(403);

        Session::put('subscribe', [
            'membership_id' => $data['membership_id'],
            'category_id' => $data['category_id']
        ]);

        return view('vendorAdmin.subscribe.payment.form')->with($data);
        //        } catch (\Exception $exception) {
        //            return redirect()->route('home');
        //        }
    }

    public function submit()
    {
        $merchant_reference = rand(0, getrandmax());
        return Payment::use($this->provider, $merchant_reference)->pay();
    }

    public function success()
    {
        try {
            $data['request'] = Session::get('request');
            if (!$data['request'])
                return redirect()->route('home');

            $data['vendor'] = Auth::guard('vendor')->user();
            $data['membership'] = Membership::findorfail(Session::get('subscribe')['membership_id']);
            $data['category'] = Category::findorfail(Session::get('subscribe')['category_id']);

            $check = $data['vendor']->current_subscribes
                ->where('category_id', $data['category']->id)
                ->where('membership_id', $data['membership']->id)->first();

            if ($check) {
                $check->update([
                    'expiry_date' => Carbon::yesterday(config('app.timezone'))
                ]);
            }

            $subscribe = Subscribe::create([
                'vendor_id' => $data['vendor']->id,
                'membership_id' => $data['membership']->id,
                'category_id' => $data['category']->id,
                'expiry_date' => Carbon::now(config('app.timezone'))->addMonth(),
                'price' => $data['membership']->price
            ]);

            $data['vendor']->update([
                'token_name' => $data['request']['token_name']
            ]);

            foreach (Admin::all() as $admin)
                $admin->notify(new VendorSubscribed($data['vendor'], $data['membership'], $data['category'], $subscribe));

            return view('vendorAdmin.subscribe.payment.success')->with($data);
        } catch (\Exception $exception) {
            return redirect()->route('home');
        }
    }

    public function error()
    {
        try {
            $data['request'] = Session::get('request');

            if (!$data['request'])
                return redirect()->route('home');

            $data['vendor'] = Auth::guard('vendor')->user();
            $data['membership'] = Membership::findorfail(Session::get('subscribe')['membership_id']);
            $data['category'] = Category::findorfail(Session::get('subscribe')['category_id']);

            return view('vendorAdmin.subscribe.payment.error')->with($data);
        } catch (\Exception $exception) {
            return redirect()->route('home');
        }
    }

    public function cancel(CancelSubscribe $request)
    {
        $data = $request->validated();

        $data['vendor'] = Auth::guard('vendor')->user();
        $data['membership'] = Membership::findorfail($data['membership_id']);
        $data['category'] = Category::findorfail($data['category_id']);

        $subscribe = $data['vendor']->current_subscribes()
            ->where('category_id', $data['category']->id)
            ->where('membership_id', $data['membership']->id)->firstorfail();

        $subscribe->update([
            'expiry_date' => Carbon::yesterday(config('app.timezone'))
        ]);

        $data['vendor']->update([
            'token_name' => null
        ]);

        foreach (Admin::all() as $admin)
            $admin->notify(new VendorSubscribeCanceled($data['vendor'], $subscribe));

        return redirect()->back();
    }
}
