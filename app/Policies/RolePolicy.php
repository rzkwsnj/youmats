<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\OrderItem;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(): bool
    {
        return true;
    }

    public function delete(Admin $admin, Role $role): bool {
        if($role->id == 1)
            return false;
        return true;
    }

    public function restore(Admin $admin, Role $role): bool {
        return true;
    }

    public function forceDelete(Admin $admin, Role $role): bool {
        return true;
    }
}
