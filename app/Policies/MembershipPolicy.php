<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class MembershipPolicy extends Policy {
    public static $key = 'memberships';
}
