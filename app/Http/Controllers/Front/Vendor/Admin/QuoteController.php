<?php

namespace App\Http\Controllers\Front\Vendor\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuoteItem;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:vendor');
        //If you would like to add "vendor verification middleware":
        $this->middleware('verified:vendor.verification.notice');
    }

    public function index() {
        $data['vendor'] = Auth::guard('vendor')->user();

        if(!($data['vendor']->current_subscribes && array_intersect($data['vendor']->current_subscribes->pluck('membership_id')->toArray(), [env('COMPANY_MEMBERSHIP_ID'), env('BOTH_MEMBERSHIP_ID')])))
            return redirect()->route('vendor.dashboard');

        $data['items'] = $data['vendor']->quote_items;

        return view('vendorAdmin.quote.index')->with($data);
    }

    public function view($id) {
        $data['vendor'] = Auth::guard('vendor')->user();

        if(!($data['vendor']->current_subscribes && array_intersect($data['vendor']->current_subscribes->pluck('membership_id')->toArray(), [env('COMPANY_MEMBERSHIP_ID'), env('BOTH_MEMBERSHIP_ID')])))
            return redirect()->route('vendor.dashboard');

        $data['item'] = QuoteItem::with('quote')->where('quote_id', $id)
            ->where('vendor_id', $data['vendor']->id)->firstorfail();

        return view('vendorAdmin.quote.view')->with($data);
    }

//    public function update(OrderRequest $request) {
//        $vendor_id = Auth::guard('vendor')->id();
//        $data = $request->validated();
//
//        $item = OrderItem::where('id', $data['item_id'])
//            ->where('vendor_id', $vendor_id)->firstorfail();
//
//        $item->update([
//            'status' => $data['status'],
//            'refused_note' => $data['refused_note']
//        ]);
//
//        Session::flash('success', __('vendorAdmin.success_update_order'));
//        return redirect()->back();
//    }
}
