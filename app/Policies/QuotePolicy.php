<?php

namespace App\Policies;

use App\Models\Admin;

class QuotePolicy extends Policy
{
    public static $key = 'quotes';

    public function create(Admin $admin) {
        return false;
    }
}
