<?php

use App\Models\City;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Stevebauman\Location\Facades\Location;

if (!function_exists('setDefaultCity')) {
    function setDefaultCity() {
        Session::put('city', 1);
    }
}

if (!function_exists('setCityUsingLocation')) {
    function setCityUsingLocation() {
        if (!Session::has('city')) {
            try {
                $ip = Request::ip();
                $location = Location::get($ip);
                if ($location) {
                    $city = City::where('name', 'LIKE', '%' . $location->cityName . '%')->select('id')->first();
                    if ($city) {
                        Session::put('city', $city->id);
                    } else {
                        setDefaultCity();
                    }
                } else {
                    setDefaultCity();
                }
            } catch (\Exception $e) {
                setDefaultCity();
            }
        }
    }
}

if (!function_exists('setCity')) {
    function setCity($city_id) {
        if(City::select('id')->find($city_id))
            Session::put('city', $city_id);
    }
}

if (!function_exists('getCurrentCityName')) {
    function getCurrentCityName() {
        $city = City::select('name')->find(Session::get('city'));
        if($city)
            return $city->name;
    }
}
