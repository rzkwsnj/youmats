<?php

namespace App\Http\Controllers\Statistics;

use App\Helpers\Classes\Log;
use Illuminate\Http\Request;

class IndexController
{
    /**
     * @param Request $request
     * @return void
     */
    public function setLog(Request $request) {

        for ($i=0; $i < count($request->input()); $i++) {
            $data = $request->validate([
                $i.'.type' => [...REQUIRED_STRING_VALIDATION, ...['In:chat,call,email']],
                $i.'.url' => NULLABLE_URL_VALIDATION,
                $i.'.id' => REQUIRED_INTEGER_VALIDATION,
                $i.'.route' => [...REQUIRED_STRING_VALIDATION, ...['In:product,category,vendor']],
                $i.'.origin' => NULLABLE_URL_VALIDATION
            ]);

            Log::set($data[$i]['type'], $data[$i]['route'], $data[$i]['id'], $data[$i]['url'], $data[$i]['origin'], $request->header('User-Agent'));

        }

    }

    public function setAction(Request $request) {

        $data = $request->validate([
            'type' => [...REQUIRED_STRING_VALIDATION, ...['In:visit,chat,call,email']],
            'url' => NULLABLE_URL_VALIDATION,
            'id' => NULLABLE_INTEGER_VALIDATION,
            'route' => NULLABLE_STRING_VALIDATION,
            'origin' => NULLABLE_URL_VALIDATION
        ]);

        Log::set($data['type'], $data['route'], $data['id'], $data['url'], $data['origin'], $request->header('User-Agent'));

    }

}
