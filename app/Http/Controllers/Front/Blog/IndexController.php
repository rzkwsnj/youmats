<?php

namespace App\Http\Controllers\Front\Blog;

use App\Http\Controllers\Controller;
use App\Models\Article;

class IndexController extends Controller
{
    public function index() {
        $data['articles'] = Article::where('active', true)->orderBy('sort')->get();

        return view('front.blog.index')->with($data);
    }

    public function show($slug) {
        $data['article'] = Article::where('active', true)->where('slug', $slug)->firstOrFail();
        $data['articles'] = Article::where('active', true)->where('slug','!=' , $slug)->orderBy('sort')->limit(3)->get();

        return view('front.blog.show')->with($data);
    }
}
