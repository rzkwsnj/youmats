<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactPolicy extends Policy {
    public static $key = 'contacts';
}
