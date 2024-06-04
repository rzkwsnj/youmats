<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\OrderItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(Admin $admin) {
        return true;
    }

    public function view(Admin $admin, OrderItem $orderItem) {
        return true;
    }

    public function create(Admin $admin) {
        return false;
    }

    public function update(Admin $admin, OrderItem $orderItem) {
        return true;
    }

    public function delete(Admin $admin, OrderItem $orderItem) {
        return true;
    }

    public function restore(Admin $admin, OrderItem $orderItem) {
        return true;
    }

    public function forceDelete(Admin $admin, OrderItem $orderItem) {
        return true;
    }
}
