<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy extends Policy {
    public static $key = 'cities';
}
