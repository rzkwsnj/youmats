<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
    use HandlesAuthorization;

    public function viewAny(Admin $admin)
    {
        return $admin->hasPermissionTo('viewAny ' . static::$key);
    }

    public function view(Admin $admin, $model)
    {
        return $admin->hasPermissionTo('view ' . static::$key);
    }

    public function create(Admin $admin)
    {
        return $admin->hasPermissionTo('create ' . static::$key);
    }

    public function update(Admin $admin, $model)
    {
        return $admin->hasPermissionTo('update ' . static::$key);
    }

    public function delete(Admin $admin, $model)
    {
        return $admin->hasPermissionTo('delete ' . static::$key);
    }

    public function restore(Admin $admin, $model)
    {
        return $admin->hasPermissionTo('restore ' . static::$key);
    }

    public function forceDelete(Admin $admin, $model)
    {
        return $admin->hasPermissionTo('forceDelete ' . static::$key);
    }
}
