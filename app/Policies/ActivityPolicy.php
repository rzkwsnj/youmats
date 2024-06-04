<?php

namespace App\Policies;

use Spatie\Activitylog\Models\Activity;
use App\Models\Admin;
use Illuminate\Auth\Access\Response;

class ActivityPolicy
{
    /**
     * Determine whether the Admin can view any models.
     */
    public function viewAny(Admin $Admin)
    {
        //
    }

    /**
     * Determine whether the Admin can view the model.
     */
    public function view(Admin $Admin, Activity $activity)
    {
        //
    }

    /**
     * Determine whether the Admin can create models.
     */
    public function create(Admin $Admin)
    {
        //
    }

    /**
     * Determine whether the Admin can update the model.
     */
    public function update(Admin $Admin, Activity $activity)
    {
        //
    }

    /**
     * Determine whether the Admin can delete the model.
     */
    public function delete(Admin $Admin, Activity $activity)
    {
        //
    }

    /**
     * Determine whether the Admin can restore the model.
     */
    public function restore(Admin $Admin, Activity $activity)
    {
        //
    }

    /**
     * Determine whether the Admin can permanently delete the model.
     */
    public function forceDelete(Admin $Admin, Activity $activity)
    {
        //
    }
}
