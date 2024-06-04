<?php

namespace App\Helpers\Classes;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AttributeFilter implements Filter
{

    public function __invoke(Builder $query, $values, string $property)
    {
        if(!is_array($values))
            $values = [$values];

        $values_temp = [];
        foreach ($values as $value) {
            $temp = explode('-', $value);
            if(count($temp) == 2) {
                $values_temp[$temp[0]][] = $temp[1];
            }
        }

        foreach ($values_temp as $value) {
            if(count($value) > 1) {
                $query->whereHas('attributes', function (Builder $query) use ($value) {
                    foreach ($value as $key => $id) {
                        if($key == 0) {
                            $query->where('attribute_values.id', $id);
                        } else {
                            $query->orWhere('attribute_values.id', $id);
                        }
                    }
                    return $query;
                });
            } else {
                $query->whereHas('attributes', function (Builder $query) use ($value) {
                    $query->where('attribute_values.id', $value[0]);
                });
            }
        }
        return $query;
    }
}
