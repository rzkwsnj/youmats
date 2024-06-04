<?php

namespace App\Nova\Metrics\Quote;

use App\Models\Quote;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class QuotesStatus extends Partition
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Quote::class, 'status')->colors([
            'pending' => '#ffed4a',
            'processing' => '#f6993f',
            'completed' => '#21b978',
            'refused' => '#e74444'
        ]);
    }

    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    public function uriKey()
    {
        return 'quote-quotes-status';
    }
}
