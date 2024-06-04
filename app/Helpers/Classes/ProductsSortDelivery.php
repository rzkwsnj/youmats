<?php

namespace App\Helpers\Classes;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ProductsSortDelivery implements Sort
{
    private $products;

    public function __construct($products) {
        $this->products = $products;
    }

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        if($descending)
            $this->products->get()->sortByDesc(function($product) {
                if(isset($product->delivery))
                    return $product->delivery['price'];
            });
        else
            $this->products->get()->sortBy(function($product) {
                if(isset($product->delivery))
                    return $product->delivery['price'];
            });
    }
}
