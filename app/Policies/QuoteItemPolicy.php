<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\QuoteItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuoteItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(Admin $admin) {
        return true;
    }

    public function view(Admin $admin, QuoteItem $orderItem) {
        return true;
    }

    public function create(Admin $admin) {
        return false;
    }

    public function update(Admin $admin, QuoteItem $orderItem) {
        return true;
    }

    public function delete(Admin $admin, QuoteItem $orderItem) {
        return true;
    }

    public function restore(Admin $admin, QuoteItem $orderItem) {
        return true;
    }

    public function forceDelete(Admin $admin, QuoteItem $orderItem) {
        return true;
    }
}
