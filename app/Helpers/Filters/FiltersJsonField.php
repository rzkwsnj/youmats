<?php

namespace App\Helpers\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FiltersJsonField implements Filter
{
    public function __invoke(Builder $query, $value, string $property) : Builder
    {
        $value = str_replace(' ', '%', $value);
        return $query->where("{$property}->en", 'LIKE',"%${value}%")
                    ->orWhere("{$property}->ar", 'LIKE',"%${value}%")
                    ->orWhere('search_keywords->en', 'LIKE',"%${value}%")
                    ->orWhere('search_keywords->ar', 'LIKE',"%${value}%");
    }
}
