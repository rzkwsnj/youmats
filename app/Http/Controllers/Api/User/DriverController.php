<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function getDriverById($id) {
        $driver = Driver::find($id);
        if($driver) {
            return new DriverResource($driver);
        }
    }
}
