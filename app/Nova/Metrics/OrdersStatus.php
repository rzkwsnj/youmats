<?php

namespace App\Nova\Metrics;

use App\Models\Order;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class OrdersStatus extends Partition
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Order::class, 'status')->colors([
            'pending' => '#ffed4a',
            'shipping' => '#f6993f',
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
        return 'orders-status';
    }
}
