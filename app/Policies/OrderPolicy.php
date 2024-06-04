<?php

namespace App\Policies;

use App\Models\Admin;

class OrderPolicy extends Policy
{
    public static $key = 'orders';

    public function create(Admin $admin) {
        return false;
    }
}
