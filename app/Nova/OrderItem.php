<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;

class OrderItem extends Resource
{
    public static $model = \App\Models\OrderItem::class;

    public static $displayInNavigation = false;
    public static $globallySearchable = false;

    public static $title = 'id';

    public static $search = [
        'id'
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('Product')
                ->readonly(),

            Number::make('Quantity')
                ->min(1),

            Currency::make('Price')
                ->rules(REQUIRED_NUMERIC_VALIDATION)
                ->min(1)
                ->step(0.05),

            Currency::make('Delivery')
                ->rules(REQUIRED_NUMERIC_VALIDATION)
                ->hideFromIndex()
                ->min(0)
                ->step(0.05),

            Currency::make('total Price')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->withMeta([
                    'value' => 'SAR ' . (($this->price * $this->quantity) + $this->delivery)
                ]),

            SimpleRepeatable::make('Delivery cars', 'delivery_cars', [
                Text::make('Car')->readonly(),
                Number::make('Quantity')->readonly(),
                Currency::make('Price')->readonly(),
                Number::make('Time')->readonly(),
                Text::make('Format')->readonly(),
                Number::make('Count')->readonly(),
                Number::make('Payload')->readonly(),
            ])->canAddRows(false)->canDeleteRows(false),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return [];
    }
}