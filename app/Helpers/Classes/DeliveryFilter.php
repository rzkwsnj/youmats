<?php

namespace App\Helpers\Classes;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class DeliveryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->where(function ($q) {
            $q->whereNotNull('shipping_id')
                ->orWhere('specific_shipping', true);
        });
    }
}
