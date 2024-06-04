<?php

namespace App\Nova\Filters\Order;

use Ampeco\Filters\DateRangeFilter;
use Carbon\Carbon;
use Laravel\Nova\Http\Requests\NovaRequest;

class OrderDate extends DateRangeFilter
{
    public function apply(NovaRequest $request, $query, $value)
    {
        $from = Carbon::parse($value[0])->startOfDay();
        $to = Carbon::parse($value[1])->endOfDay();

        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function options(NovaRequest $request)
    {
        return [];
    }
}
