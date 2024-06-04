<?php

namespace App\Http\Controllers\Front\User\Auth;

use App\Http\Controllers\Controller;
use App\Mail\NewRegister;
use App\Models\Admin;
use App\Models\Country;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Notifications\CompanyRegistered;
use App\Rules\PhoneNumberRule;
use App\Rules\TopLevelEmailDomainValidator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param Request $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if($validator->fails())
            return redirect(url()->previous() . '#register-tab')->withInput()->withErrors($validator);

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * @return Application|Factory|View
     */
    public function showRegistrationForm()
    {
        $countries = Country::all();

        return view('auth.register', compact('countries'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'type' => ['required', 'string', 'In:individual,company'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users', new TopLevelEmailDomainValidator()],
            'phone' => ['nullable', new PhoneNumberRule()],
            'address' => ['nullable', 'string', 'max:191'],
            'latitude' => NULLABLE_STRING_VALIDATION,
            'longitude' => NULLABLE_STRING_VALIDATION,
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'files' => ['required_if:type,company', 'array'],
            'files.*' => NULLABLE_FILE_VALIDATION
        ]);
    }

    protected function create(array $data)
    {
        $user = User::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'password' => Hash::make($data['password']),
        ]);

        if(isset($data['files']))
            foreach($data['files'] as $file)
                $user->addMedia($file)->toMediaCollection(COMPANY_PATH);

        if($data['type'] == 'company')
            foreach(Admin::all() as $admin)
                $admin->notify(new CompanyRegistered($user));

        if($user)
            try {
                Mail::to([
                    'info@youmats.com',
                    'sameh@youmats.com',
                    'ereny@youmats.com',
                    'marina@youmats.com'
                ])->send(new NewRegister($user));
            } catch(\Exception $e) {}
        Session::flash('custom_success', __('auth.user_register_successfully'));

        return $user;
    }
}
