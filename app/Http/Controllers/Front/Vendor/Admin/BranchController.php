<?php

namespace App\Http\Controllers\Front\Vendor\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\BranchRequest;
use App\Models\City;
use App\Models\VendorBranch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:vendor');
        //If you would like to add "vendor verification middleware":
        $this->middleware('verified:vendor.verification.notice');
    }

    public function index() {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['branches'] = $data['vendor']->branches;

        return view('vendorAdmin.branch.index')->with($data);
    }

    public function create() {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['cities'] = City::where('country_id', $data['vendor']->country_id)->get();

        return view('vendorAdmin.branch.create')->with($data);
    }

    public function store(BranchRequest $request) {
        $data = $request->validated();
        $vendor_id = Auth::guard('vendor')->id();

        $data['vendor_id'] = $vendor_id;

        $data['name'] = ['en' => $data['name_en'], 'ar' => $data['name_ar']];

        VendorBranch::create($data);

        Session::flash('success', __('vendorAdmin.success_store_branch'));
        return redirect()->route('vendor.branch.index');
    }

    public function edit($branch_id) {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['branch'] = VendorBranch::where([
            'id' => $branch_id,
            'vendor_id' => $data['vendor']->id
        ])->firstorfail();
        $data['cities'] = City::where('country_id', $data['vendor']->country_id)->get();

        return view('vendorAdmin.branch.edit')->with($data);
    }

    public function update(BranchRequest $request, $branch_id) {
        $data = $request->validated();
        $vendor_id = Auth::guard('vendor')->id();

        $branch = VendorBranch::where('id', $branch_id)->where('vendor_id', $vendor_id)->firstorfail();

        $data['name'] = ['en' => $data['name_en'], 'ar' => $data['name_ar']];

        $branch->update($data);

        Session::flash('success', __('vendorAdmin.success_update_branch'));
        return redirect()->back();
    }

    public function delete($branch_id) {
        $vendor_id = Auth::guard('vendor')->id();

        $branch = VendorBranch::where('id', $branch_id)->where('vendor_id', $vendor_id)->firstorfail();

        $branch->delete();

        Session::flash('success', __('vendorAdmin.success_delete_branch'));
        return redirect()->route('vendor.branch.index');
    }
}
