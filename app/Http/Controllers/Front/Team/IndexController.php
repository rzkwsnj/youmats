<?php

namespace App\Http\Controllers\Front\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;

class IndexController extends Controller
{
    public function index() {
        $data['team'] = Team::where('active', true)->orderBy('sort')->get();

        return view('front.team.index')->with($data);
    }
}
