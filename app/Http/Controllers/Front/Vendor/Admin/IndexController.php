<?php


namespace App\Http\Controllers\Front\Vendor\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\VendorRequest;
use App\Models\Admin;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\City;
use App\Models\Vendor;
use App\Models\VendorBranch;
use App\Notifications\OrderCreated;
use App\Notifications\VendorUpdated;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class IndexController extends Controller
{
    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:vendor');

        //If you would like to add "vendor verification middleware":
        $this->middleware('verified:vendor.verification.notice');
    }

    public function dashboard() {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['products'] = count($data['vendor']->products);
        $data['branches'] = count($data['vendor']->branches);
        $data['shippingGroups'] = count($data['vendor']->shippings);
        $data['contacts'] = count($data['vendor']->contacts);
        $data['orders'] = count($data['vendor']->order_items);
        $data['quotes'] = count($data['vendor']->quote_items);

        return view('vendorAdmin.dashboard')->with($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|Factory|View
     */
    public function edit(Request $request)
    {
        $data['vendor'] = Auth::guard('vendor')->user();
        $data['cities'] = City::where('country_id', $data['vendor']->country_id)->get();

        return view('vendorAdmin.edit')->with($data);
    }

    /**
     * @param VendorRequest $request
     * @return RedirectResponse
     */
    public function update(VendorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $vendor = Auth::guard('vendor')->user();

        if(isset($data['logo'])) {
            //Delete previously created logos
            $vendor->clearMediaCollectionExcept(VENDOR_LOGO);
            //Add new logo!
            $vendor->addMedia($data['logo'])->toMediaCollection(VENDOR_LOGO);
        }
        if(isset($data['cover'])) {
            //Delete previously created covers
            $vendor->clearMediaCollectionExcept(VENDOR_COVER);
            //Add new cover!
            $vendor->addMedia($data['cover'])->toMediaCollection(VENDOR_COVER);
        }

        //Tweak the system to create a new password for the vendor only if its set.
        if(isset($data['password']))
            $data['password'] = Hash::make($data['password']);
        else
            $data['password'] = $vendor->password;

        $vendor->setTranslation('name', 'en', $data['name_en']);
        $vendor->setTranslation('name', 'ar', $data['name_ar']);

        if(isset($data['contacts_person_name'])) {
            for ($i=0;$i<count($data['contacts_person_name']);$i++) {
                if(!is_array($data['contacts_cities'][$i])) {
                    $data['contacts_cities'][$i] = [$data['contacts_cities'][$i]];
                }
                $data['contacts'][] = [
                    'person_name' => $data['contacts_person_name'][$i],
                    'email' => $data['contacts_email'][$i],
                    'call_phone' => $data['contacts_call_phone'][$i],
                    'phone' => $data['contacts_phone'][$i],
                    'cities' => $data['contacts_cities'][$i],
                    'with' => $data['contacts_with'][$i],
                ];
            }
        } else {
            $data['contacts'] = [];
        }

        if(isset($data['licenses'])
            || $data['name_en'] != $vendor->getTranslation('name', 'en')
            || $data['name_ar'] != $vendor->getTranslation('name', 'ar')
            || $data['email'] != $vendor->email
            || $data['phone'] != $vendor->phone
            || $data['address'] != $vendor->address) {
            $data['active'] = false;

            foreach(Admin::all() as $admin)
                $admin->notify(new VendorUpdated($vendor));
        }

        $vendor->update($data);

        // Add licenses to the vendor
        if(isset($data['licenses'])) {
            foreach($data['licenses'] as $license) {
                $vendor->addMedia($license)->toMediaCollection(VENDOR_PATH);
            }
        }

        Session::flash('success', __('vendorAdmin.success_update_vendor'));
        return redirect()->back();
    }


    /**
     * @param Request $request
     * @param false $has_template
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getSubCategories(Request $request, $has_template = false) {
        $data = $this->validate($request, [
            'category_id' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:categories,id']]
        ]);

        if($has_template) {
            $subCategories = Category::where('parent_id', $data['category_id'])->where('template', '!=', '[]')->orderBy('sort')->pluck('name', 'id');
        }
        else
            $subCategories = Category::where('parent_id', $data['category_id'])->orderBy('sort')->pluck('name', 'id');

        return response()->json($subCategories);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getAttributes(Request $request): JsonResponse
    {
        $data = $this->validate($request, [
            'subCategory_id' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:categories,id']]
        ]);

        $attributes = Attribute::with('values')->where('category_id', $data['subCategory_id'])
            ->orderBy('sort')->get();

        return response()->json($attributes);
    }

    public function getTemplate(Request $request) {
        $data = $this->validate($request, [
            'subCategory_id' => [...REQUIRED_INTEGER_VALIDATION, ...['exists:categories,id']]
        ]);

        $template = Category::where('id', $data['subCategory_id'])->pluck('template');

        return response()->json($template[0]);
    }
}