<?php

namespace App\View\Composers;

use App\Models\Category;
use App\Models\Currency;
use App\Models\StaticPage;
use Illuminate\View\View;

class GeneralComposer
{
    public function compose(View $view)
    {
        $data['categories'] = Category::with([
            'ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug'),
            'children' => fn ($q) => $q->with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
                ->select('id', 'parent_id', '_lft', '_rgt', 'name', 'slug'),
            'media'
        ])
            ->withDepth()->having('depth', '=', 0)
            ->where('category', '1')->orderBy('sort')
            ->get(['id', 'parent_id', '_lft', '_rgt', 'name', 'slug']);

        $data['currencies'] = Currency::where('active', true)->orderBy('sort')->get(['id', 'name', 'code', 'symbol']);
        $data['pages'] = StaticPage::orderBy('sort')->get(['title', 'slug']);
        $data['footer_categories'] = Category::with(['ancestors' => fn ($q) => $q->select('id', 'parent_id', '_lft', '_rgt', 'slug')])
            ->where('show_in_footer', true)->get(['id', 'parent_id', '_lft', '_rgt', 'name', 'slug']);

        $view->with($data);
    }
}
