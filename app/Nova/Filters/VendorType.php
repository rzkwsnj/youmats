<?php

namespace App\Nova\Filters;

use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Filters\Filter;

class VendorType extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        if($value == 'have-products')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) > ?', [0]);
        elseif($value == 'have-products-contacts')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '!=', '[]')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) > ?', [0]);
        elseif($value == 'have-products-no-contacts')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '[]')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) > ?', [0]);
        elseif($value == 'have-products-contacts-subscribed')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '!=', '"[]"')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) > ?', [0])
                ->whereHas('current_subscribes', function ($query) {
                    $query;
                 });
        elseif($value == 'have-products-no-contacts-no-subscribed')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '"[]"')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) > ?', [0])
                ->whereDoesntHave('current_subscribes', function ($query) {
                    $query;
                });
        elseif($value == 'have-not-products')
            return $query->select('vendors.*')
                ->leftJoin('products', 'products.vendor_id', 'vendors.id')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) = ?', [0]);
        elseif($value == 'have-not-products-contacts')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '!=', '[]')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) = ?', [0]);
        elseif($value == 'have-not-products-no-contacts')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '[]')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) = ?', [0]);
        elseif($value == 'have-not-products-contacts-subscribed')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '!=', '"[]"')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) = ?', [0])
                ->whereHas('current_subscribes', function ($query) {
                    $query;
                 });
        elseif($value == 'have-not-products-no-contacts-no-subscribed')
            return $query->select('vendors.*')
                ->join('products', 'products.vendor_id', 'vendors.id')
                ->whereNull('products.deleted_at')
                ->where('vendors.contacts', '"[]"')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) = ?', [0])
                ->whereDoesntHave('current_subscribes', function ($query) {
                    $query;
                });
        elseif($value == 'have-products-but-soft-deleted')
            return $query->select('vendors.*')
                ->leftJoin('products', 'products.vendor_id', 'vendors.id')
                ->whereNotNull('products.deleted_at')
                ->groupBy(['vendors.id'])
                ->havingRaw('COUNT(products.id) = (SELECT COUNT(`products`.`id`) FROM `products` WHERE `products`.`vendor_id` = `vendors`.`id`)');


        }

    /**
     * Get the filter's available options.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return [
            'Has Products' => 'have-products',
            'Has Products & Has Contacts' => 'have-products-contacts',
            'Has Products & No Contacts' => 'have-products-no-contacts',
            'Has Products & Has Contacts & subscribed' => 'have-products-contacts-subscribed',
            'Has Products & No Contacts & Not subscribed' => 'have-products-no-contacts-no-subscribed',
            'Has no Products' => 'have-not-products',
            'Has no Products & Has Contacts' => 'have-not-products-contacts',
            'Has no Products & No Contacts' => 'have-not-products-no-contacts',
            'Has no Products & Has Contacts & subscribed' => 'have-not-products-contacts-subscribed',
            'Has no Products & No Contacts & Not subscribed' => 'have-not-products-no-contacts-no-subscribed',
            'Have Products But SoftDeleted' => 'have-products-but-soft-deleted'
        ];
    }
}
