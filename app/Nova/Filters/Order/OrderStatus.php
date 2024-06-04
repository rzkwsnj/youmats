<?php

namespace App\Nova\Filters\Order;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Filters\Filter;

class OrderStatus extends Filter
{
    public $component = 'select-filter';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('status', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return [
            'Pending' => 'pending',
            'Shipping' => 'shipping',
            'Completed' => 'completed',
            'Refused' => 'refused'
        ];
    }
}