<?php

namespace App\Nova\Filters\Quote;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class QuoteStatus extends Filter
{
    public $component = 'select-filter';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('status', $value);
    }

    public function options(NovaRequest $request)
    {
        return [
            'Pending' => 'pending',
            'Processing' => 'processing',
            'Completed' => 'completed',
            'Refused' => 'refused'
        ];
    }
}
