<?php

namespace App\Nova\Metrics\Quote;

use App\Models\Quote;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class QuotePerDay extends Trend
{
    public function calculate(NovaRequest $request)
    {
        return $this->countByDays($request, Quote::class)->showSumValue();
    }

    public function ranges()
    {
        return [
            7 => __('7 Days'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days'),
        ];
    }

    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    public function uriKey()
    {
        return 'quote-quote-per-day';
    }
}
