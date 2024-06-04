<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Ladumor\OneSignal\OneSignal;

class GeneralController extends Controller
{
    public function socialLinks() {
        return nova_get_settings(['facebook', 'twitter']);
    }

    public function getAllNotifications() {
        return OneSignal::getNotifications();
    }

//    public function pushNotifications() {
//        $user_id = Auth::guard('api')->id();
//        $fields['include_player_ids'] = [];
//        $notificationMsg = 'Maher bymsi :D';
//
//        OneSignal::sendPush($fields, $notificationMsg);
//    }
}
