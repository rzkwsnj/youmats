<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function before($admin, $ability) {
        if ($admin->isSuperAdmin()) {
            return true;
        }
    }

    public function viewAny(Admin $admin)
    {
        //
    }

    public function view(Admin $admin, Admin $admin2)
    {
        //
    }


    public function create(Admin $admin)
    {
        //
    }


    public function update(Admin $admin, Admin $admin2)
    {
        //
    }


    public function delete(Admin $admin, Admin $admin2)
    {
        //
    }


    public function restore(Admin $admin, Admin $admin2)
    {
        //
    }


    public function forceDelete(Admin $admin, Admin $admin2)
    {
        //
    }
}
