<?php

namespace App\Nova\Metrics;

use App\Models\Subscriber;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SubscribersCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(Subscriber::count())->suffix('Subscribers');
    }

    public function ranges()
    {
        return [];
    }

    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    public function uriKey()
    {
        return 'subscribers-count';
    }
}
