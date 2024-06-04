<?php

namespace App\Http\Controllers\Front\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    public function index() {
        $user = auth()->user();

        if($user->type == 'company')
            $data = $user->quotes;
        else
            $data = $user->orders;

        return view('front.user.profile')->with(compact('user', 'data'));
    }

    public function updateProfile(UserProfileRequest $request) {
        $data = $request->validated();
        $auth_user = User::findOrFail(auth()->user()->id);

        if(isset($request->profile)) {
            $auth_user->clearMediaCollection(USER_PROFILE);
            $auth_user->addMedia($request->profile)->toMediaCollection(USER_PROFILE);
        }
        if(isset($request->cover)) {
            $auth_user->clearMediaCollection(USER_COVER);
            $auth_user->addMedia($request->cover)->toMediaCollection(USER_COVER);
        }
        if(isset($request->licenses)) {
            if(!count($auth_user->getMedia(COMPANY_PATH))) {
                foreach ($request->licenses as $license) {
                    $auth_user->addMedia($license)->toMediaCollection(COMPANY_PATH);
                }
            }
        }

//        if($request->email != $auth_user->email)
//            $data['email_verified_at'] = null;

        if(isset($request->password))
            $data['password'] = Hash::make($request->password);
        else
            unset($data['password']);

        $auth_user->update($data);

        Session::flash('custom_success', __('Profile has been updated successfully!'));
        return back();
    }
}
