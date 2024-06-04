<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\ForgetPasswordRequest;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\Api\User\ProfileUpdateRequest;
use App\Http\Requests\Api\User\RegisterRequest;
use App\Http\Requests\Api\User\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function login(LoginRequest $request) {
        $data = $request->validated();

        if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return response(['message' => 'Invalid login credentials.'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('authToken')->accessToken;

        return (new UserResource($user))->additional([
            'player_id' => $data['player_id'],
            'token' => $token
        ]);
    }

    public function register(RegisterRequest $request) {
        $data = $request->validated();

        $user = User::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'password' => Hash::make($data['password']),
            'player_id' => $data['player_id']
        ]);

        $user = Auth::loginUsingId($user->id);
        $token = $user->createToken('authToken')->accessToken;

        return (new UserResource($user))->additional([
            'player_id' => $data['player_id'],
            'token' => $token
        ]);
    }

    public function password_forgot(ForgetPasswordRequest $request) {
        $data = $request->validated();

        $response = Password::sendResetLink(['email' => $data['email']]);

        return $response == Password::RESET_LINK_SENT
            ? Response::json(array("message" => trans($response)), 200)
            : Response::json(array("message" => trans($response)),400);
    }

    public function password_reset(ResetPasswordRequest $request) {
        $data = $request->validated();
        $user = Auth::user();

        if ((Hash::check($data['old_password'], $user->password)) == false) {
            $response = array("message" => "Check your old password.");
            $status = 400;
        } else if ((Hash::check($data['new_password'], $user->password)) == true) {
            $response = array("message" => "Please enter a password which is not similar then current password.");
            $status = 400;
        } else {
            User::where('id', $user->id)->update(['password' => Hash::make($data['new_password'])]);
            $response = array("message" => "Password updated successfully.");
            $status = 200;
        }

        return Response::json($response, $status);
    }

    public function profile_update(ProfileUpdateRequest $request) {
        $data = $request->validated();
        $user = Auth::user();

        if(isset($request->profile)) {
            $user->clearMediaCollection(USER_PROFILE);
            $user->addMedia($request->profile)->toMediaCollection(USER_PROFILE);
        }

        if(isset($request->password))
            $data['password'] = Hash::make($request->password);
        else
            unset($data['password']);

        $user->update($data);

        return (new UserResource($user));
    }
}
