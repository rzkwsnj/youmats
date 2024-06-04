<?php

use Illuminate\Support\Facades\Session;
use App\Models\Currency;

if (!function_exists('checkCurrencyLocation')) {
    function checkCurrencyLocation() {
        try {
            $ip = request()->ip();
            $location = geoip($ip);
            $currencyCode = $location->currency;
        } catch (\Exception $e) {
            $currencyCode = 'SAR';
        }
        return $currencyCode;
    }
}

if (!function_exists('setCurrency')) {
    function setCurrency($code) {
        $currency = Currency::where('code', $code)->select('name', 'code', 'symbol', 'rate')->first();
        if(!$currency)
            $currency = Currency::select('name', 'code', 'symbol', 'rate')->first();
        Session::put('currency', $currency);
    }
}

if (!function_exists('getCurrency')) {
    function getCurrency($value) {
        if(!Session::has('currency'))
            setCurrency('SAR');
        return Session::get('currency')[$value];
    }
}
