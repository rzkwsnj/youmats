<?php

namespace App\Http\Controllers\Front\Vendor\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\ShippingGroupRequest;
use App\Models\City;
use App\Models\Shipping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SippingGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:vendor');
        //If you would like to add "vendor verification middleware":
        $this->middleware('verified:vendor.verification.notice');
    }

    public function index() {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['shipping_prices'] = $data['vendor']->shippings;

        return view('vendorAdmin.shippingGroup.index')->with($data);
    }

    public function create() {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['cities'] = City::where('country_id', $data['vendor']->country_id)->get();

        return view('vendorAdmin.shippingGroup.create')->with($data);
    }

    public function store(ShippingGroupRequest $request) {
        $data = $request->all();
        $vendor_id = Auth::guard('vendor')->id();

        $data['vendor_id'] = $vendor_id;

        if(isset($data['cars'])) {
            foreach ($data['cars'] as $key => $car) {
                $data['prices'][$key]['layout'] = 'cars';
                $data['prices'][$key]['key'] = Str::random(16);
                $data['prices'][$key]['attributes']['car_type'] = $car['car_type'];
                unset($car['car_type']);
                foreach ($car as $city) {
                    $data['prices'][$key]['attributes']['cities'][] = [
                        'layout' => 'cities',
                        'key' => Str::random(16),
                        'attributes' => $city
                    ];
                }
            }
        } else {
            $data['prices'] = [];
        }

        Shipping::create($data);

        Session::flash('success', __('vendorAdmin.success_store_shipping'));
        return redirect()->route('vendor.shipping-group.index');
    }

    public function edit($id) {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['shipping'] = Shipping::where('id', $id)->where('vendor_id', $data['vendor']->id)->firstorfail();
        $data['cities'] = City::where('country_id', $data['vendor']->country_id)->get();

        return view('vendorAdmin.shippingGroup.edit')->with($data);
    }

    public function update(ShippingGroupRequest $request, $shipping_id) {
        $data = $request->all();
        $vendor_id = Auth::guard('vendor')->id();

        $shipping = Shipping::where('id', $shipping_id)->where('vendor_id', $vendor_id)->firstorfail();

        if(isset($data['cars'])) {
            foreach ($data['cars'] as $key => $car) {
                $data['prices'][$key]['layout'] = 'cars';
                $data['prices'][$key]['key'] = Str::random(16);
                $data['prices'][$key]['attributes']['car_type'] = $car['car_type'];
                unset($car['car_type']);
                foreach ($car as $city) {
                    $data['prices'][$key]['attributes']['cities'][] = [
                        'layout' => 'cities',
                        'key' => Str::random(16),
                        'attributes' => $city
                    ];
                }
            }
        } else {
            $data['prices'] = [];
        }

        $shipping->update($data);

        Session::flash('success', __('vendorAdmin.success_update_shipping'));
        return redirect()->back();
    }

    public function delete($shipping_id) {
        $vendor_id = Auth::guard('vendor')->id();

        $shipping = Shipping::where('id', $shipping_id)->where('vendor_id', $vendor_id)->firstorfail();

        $shipping->delete();

        Session::flash('success', __('vendorAdmin.success_delete_shipping'));
        return redirect()->route('vendor.shipping-group.index');
    }
}
