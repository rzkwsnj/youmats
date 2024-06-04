<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy extends Policy {
    public static $key = 'currencies';

    public function delete(Admin $admin, $model) {
        if($model->code == 'SAR')
            return false;
        else
            return parent::delete($admin, $model);
    }

    public function restore(Admin $admin, $model) {
        if($model->code == 'SAR')
            return false;
        else
            return parent::restore($admin, $model);
    }

    public function forceDelete(Admin $admin, $model) {
        if($model->code == 'SAR')
            return false;
        else
            return parent::forceDelete($admin, $model);
    }
}

