<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Driver\ForgetPasswordRequest;
use App\Http\Requests\Api\Driver\LoginRequest;
use App\Http\Requests\Api\Driver\ProfileUpdatePhotosRequest;
use App\Http\Requests\Api\Driver\ProfileUpdateRequest;
use App\Http\Requests\Api\Driver\RegisterRequest;
use App\Http\Requests\Api\Driver\ResetPasswordRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request) {
        $login = $request->validated();

        if (!Auth::guard('driver')->attempt($login)) {
            return response(['message' => 'Invalid login credentials.'], 400);
        }
        $driver = Auth::guard('driver')->user();
        if(!$driver->active) {
            Auth::guard('driver')->logout();
            return response(['message' => __('messages.not_active_yet')], 400);
        }
        $token = $driver->createToken('authToken')->accessToken;

        return (new DriverResource($driver))->additional([
            'token' => $token
        ]);
    }

    public function register(RegisterRequest $request) {
        $data = $request->validated();

        $driver = Driver::create([
            'country_id' => $data['country_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'active' => '0'
        ]);

        $driver = Auth::guard('driver')->loginUsingId($driver->id);
        $token = $driver->createToken('authToken')->accessToken;

        return (new DriverResource($driver))->additional([
            'token' => $token,
        ]);

//        return response()->json(['message' => __('messages.wait_for_approve')]);
    }

    public function password_forgot(ForgetPasswordRequest $request) {
        $data = $request->validated();

        $response = Password::broker('drivers')->sendResetLink(['email' => $data['email']]);

        return $response == Password::RESET_LINK_SENT
            ? Response::json(array("message" => trans($response)), 200)
            : Response::json(array("message" => trans($response)),400);
    }

    public function password_reset(ResetPasswordRequest $request) {
        $data = $request->validated();
        $driver = Auth::guard('driver-api')->user();

        if ((Hash::check($data['old_password'], $driver->password)) == false) {
            $response = array("message" => "Check your old password.");
            $status = 400;
        } else if ((Hash::check($data['new_password'], $driver->password)) == true) {
            $response = array("message" => "Please enter a password which is not similar then current password.");
            $status = 400;
        } else {
            Driver::where('id', $driver->id)->update(['password' => Hash::make($data['new_password'])]);
            $response = array("message" => "Password updated successfully.");
            $status = 200;
        }

        return Response::json($response, $status);
    }

    public function profile_update(ProfileUpdateRequest $request) {
        $data = $request->validated();
        $driver = Auth::user();

        $this->uploadDriverPhotos($request, $driver);

        if(isset($request->password))
            $data['password'] = Hash::make($request->password);
        else
            unset($data['password']);

        $driver->update($data);

        return (new DriverResource($driver));
    }

    public function updatePhotos(ProfileUpdatePhotosRequest $request) {
//        $data = $request->validated();
        $driver = Auth::user();

        $this->uploadDriverPhotos($request, $driver);

        return (new DriverResource($driver));
    }

    private function uploadDriverPhotos($request, $driver) {
        if(isset($request->driver_photo)) {
            foreach ($request->driver_photo as $img)
                $driver->addMedia($img)->toMediaCollection(DRIVER_PHOTO);
        }
        if(isset($request->driver_id)) {
            foreach ($request->driver_id as $img)
                $driver->addMedia($img)->toMediaCollection(DRIVER_ID);
        }
        if(isset($request->driver_license)) {
            foreach ($request->driver_license as $img)
                $driver->addMedia($img)->toMediaCollection(DRIVER_LICENSE);
        }
    }
}
