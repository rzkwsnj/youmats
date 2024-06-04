<?php

namespace App\Nova\Metrics;

use App\Models\Category;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class CategoryCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(Category::count())->suffix('Categories');
    }

    public function ranges()
    {
        return [
        ];
    }

    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    public function uriKey()
    {
        return 'category-count';
    }
}
