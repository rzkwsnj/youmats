<?php

namespace App\Nova\Metrics;

use App\Models\Product;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ProductCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(Product::count())->suffix('Products');
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
        return 'product-count';
    }
}
