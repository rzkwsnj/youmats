<?php

namespace App\Http\Controllers\Front\Vendor\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\OrderRequest;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:vendor');
        //If you would like to add "vendor verification middleware":
        $this->middleware('verified:vendor.verification.notice');
    }

    public function index() {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['items'] = $data['vendor']->order_items;

        return view('vendorAdmin.order.index')->with($data);
    }

    public function edit($id) {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['item'] = OrderItem::with('order')->where('order_id', $id)
            ->where('vendor_id', $data['vendor']->id)->firstorfail();

        return view('vendorAdmin.order.edit')->with($data);
    }

    public function update(OrderRequest $request) {
        $vendor_id = Auth::guard('vendor')->id();
        $data = $request->validated();

        $item = OrderItem::where('id', $data['item_id'])
                ->where('vendor_id', $vendor_id)->firstorfail();

        $item->update([
            'status' => $data['status'],
            'refused_note' => $data['refused_note']
        ]);

        Session::flash('success', __('vendorAdmin.success_update_order'));
        return redirect()->back();
    }
}
