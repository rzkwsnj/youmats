<?php

namespace App\Http\Controllers\Front\Vendor;

use App\Helpers\Classes\Log;
use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class IndexController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        // Get all vendors
        $vendors = Vendor::with(['media'])->whereActive(true)->select(['id', 'name', 'slug'])->paginate(21);
        $vendors->withPath(url()->current())->withQueryString();

        // Return the vendors to the view.
        // So we can loop through
        return view('front.vendor.index', ['vendors' => $vendors]);
    }

    /**
     * @param $vendor_slug
     * @return Application|Factory|View
     */
    public function show($vendor_slug)
    {

        $this->checkOnSupplierSlug($vendor_slug);

        $data['vendor'] = Vendor::with([
            'branches' => fn ($q) => $q->with([
                'city' => fn ($q) => $q->select('id', 'name')
            ])->select('id', 'city_id', 'name', 'latitude', 'longitude', 'phone_number', 'fax', 'website', 'address')
        ])->where('slug', $vendor_slug)->firstorfail();

        $data['products'] = $data['vendor']->products()->with([
            'category' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug', 'hide_delivery_status'),
            'media',
            'vendor' => fn ($q) => $q->select('id', 'name', 'slug', 'contacts', 'enable_3cx', 'enable_encryption_mode', 'manage_by_admin', 'sold_by_youmats')
        ])->paginate(20);

        if ($data['vendor']->subscribe && !$data['vendor']->manage_by_admin && $data['vendor']->contacts) {
            $data['widget_phone'] = Clean_Phone_Number(get_contact($data['vendor'], 'call_phone'));
            $data['widget_whatsapp'] = $data['vendor']->whatsapp_message();
        }


        return view('front.vendor.show')->with($data);
    }

    private function checkOnSupplierSlug($vendor_slug)
    {

        $vendor = Vendor::with([
            'branches' => fn ($q) => $q->with(['city' => fn ($q) => $q->select('id', 'name')])
                ->select('id', 'city_id', 'name', 'latitude', 'longitude', 'phone_number', 'fax', 'website', 'address')
        ])->where('slug', $vendor_slug)->first();


        if (!is_null($vendor)) {
            $correct_link = route('vendor.show', [$vendor->slug]);
            if ($correct_link != url()->current()) {
                redirect()->to($correct_link, 301)->send();
            }
        } else {
            redirect()->to("/suppliers", 301)->send();
        }
    }
}
