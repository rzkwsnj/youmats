<?php

namespace App\Nova\Filters\Order;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Filters\Filter;

class PaymentMethod extends Filter {
    public $component = 'select-filter';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('payment_method', $value);
    }

    public function options(NovaRequest $request)
    {
        return [
            'Cash' => 'cash',
            'Credit Card' => 'credit card',
            'PayPal' => 'paypal'
        ];
    }
}
