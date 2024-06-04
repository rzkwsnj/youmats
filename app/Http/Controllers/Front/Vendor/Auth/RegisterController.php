<?php

namespace App\Http\Controllers\Front\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewRegister;
use App\Models\Country;
use App\Models\Vendor;
use App\Providers\RouteServiceProvider;
use App\Rules\PhoneNumberRule;
use App\Rules\TopLevelEmailDomainValidator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * @var string
     */
    protected string $redirectTo = RouteServiceProvider::VENDOR_HOME;

    /**
     * RegisterController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest:vendor');
    }

    /**
     * @param Request $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if($validator->fails())
            return redirect(url()->previous() . '#vendor-registration-tab')->withInput()->withErrors($validator);

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    public function showRegistrationForm()
    {
        $countries = Country::all();

        return view('front.vendor.auth.register', compact('countries'));
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'country_id' => ['required', 'numeric', 'exists:countries,id'],
            'name_en' => ['required', 'string', 'max:191'],
            'name_ar' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'max:191', 'email', 'unique:vendors', new TopLevelEmailDomainValidator()],
            'phone' => ['required', new PhoneNumberRule()],
            'address' => ['required', 'string', 'max:191'],
            'type' => [...NULLABLE_STRING_VALIDATION, 'In:factory,distributor,wholesales,retail'],
            'latitude' => NULLABLE_STRING_VALIDATION,
            'longitude' => NULLABLE_STRING_VALIDATION,
            'licenses' => ARRAY_VALIDATION,
            'licenses.*' => REQUIRED_FILE_VALIDATION,
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'contract' => 'required'
        ]);
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function create(array $data)
    {
        $vendor = Vendor::create([
            'country_id' => $data['country_id'],
            'name' => $data['name_en'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'type' => $data['type'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'password' => Hash::make($data['password']),
            'slug' => $data['name_en'] . rand(1,99)
        ]);

        $vendor->setTranslation('name', 'en', $data['name_en']);
        $vendor->setTranslation('name', 'ar', $data['name_ar']);

        // Add licenses to the vendor
        if(isset($data['licenses']))
            foreach($data['licenses'] as $license) {
                $vendor->addMedia($license)->toMediaCollection(VENDOR_PATH);
            }

        $vendor->save();

        if($vendor)
            try {
                Mail::to([
                    'info@youmats.com',
                    'sameh@youmats.com',
                    'ereny@youmats.com',
                    'marina@youmats.com'
                ])->send(new NewRegister($vendor));
            } catch(\Exception $e) {}

        Session::flash('custom_success', __('auth.vendor_register_successfully'));

        return $vendor;
    }

    /**
     * @return Guard|StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('vendor');
    }
}
