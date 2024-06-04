<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function index(Request $request) {

        if(isset($request->filter['city'])) {
            setCity($request->filter['city']);
        }

        $data['sliders'] = Slider::with('media')->where('active', true)->orderBy('sort')
            ->get(['id', 'quote', 'title', 'button_link', 'button_title']);

        $data['featured_categories'] = Category::with([
            'ancestors' => fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug'),
            'media'
        ])->where('isFeatured', true)->orderBy('sort')->take(9)
            ->get(['id', 'parent_id', '_lft', '_rgt', 'name', 'slug']);

        $data['best_seller_products'] = Product::with([
            'category' => fn($q) => $q->with(['ancestors' => fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'name', 'parent_id', '_lft', '_rgt', 'slug'),
            'media'
        ])->where('active', true)
          ->orderBy('updated_at', 'DESC')
          ->take(14)->get(['id', 'category_id', 'name', 'slug']);

        $data['top_categories'] = Category::with([
            'ancestors' => fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug'),
            'media'
        ])->where('topCategory', true)->orderBy('sort')->take(6)
            ->get(['id', 'parent_id', '_lft', '_rgt', 'name', 'slug']);

        $data['featured_sections_categories'] = Category::with([
            'ancestors' => fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug'),
            'media'
        ])
        ->where('featured_sections', true)->orderBy('featured_section_order')
        ->get(['id', 'parent_id', '_lft', '_rgt', 'name', 'slug']);

        return view('front.index')->with($data);
    }
}
