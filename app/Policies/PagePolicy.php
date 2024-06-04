<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy extends Policy {
    public static $key = 'pages';
}
