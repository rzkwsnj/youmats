<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ladumor\OneSignal\OneSignal;

class GeneralController extends Controller
{
    public function socialLinks() {
        return nova_get_settings(['facebook', 'twitter']);
    }

    public function getAllNotifications() {
        return OneSignal::getNotifications();
    }
}
