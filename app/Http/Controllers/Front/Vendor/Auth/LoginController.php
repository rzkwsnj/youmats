<?php

namespace App\Http\Controllers\Front\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected string $redirectTo = RouteServiceProvider::HOME;

    public function __construct() {
        $this->redirectTo = url()->previous();
        $this->middleware('guest:vendor')->except('logout');
    }

    protected function showLoginForm()
    {
        return view('front.vendor.auth.login');
    }

    protected function guard()
    {
        return Auth::guard('vendor');
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Response|Redirector
     */
    protected function loggedOut(Request $request)
    {
        return $request->wantsJson()
            ? new Response('', 204)
            : redirect($this->redirectTo);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request): array
    {
        return [
            'email' => $request->email,
            'password' => $request->password
        ];
    }

    /**
     * @param Request $request
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')]
        ])->redirectTo($this->redirectTo . '#vendor-login-tab');
    }

    /**
     * @param Request $request
     * @return Application|JsonResponse|RedirectResponse|Response|Redirector
     */
    protected function logout(Request $request)
    {
        $sessionKey = $this->guard()->getName();

        $this->guard()->logout();

        $request->session()->forget($sessionKey);

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }
}
