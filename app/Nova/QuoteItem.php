<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class QuoteItem extends Resource
{
    public static $model = \App\Models\QuoteItem::class;

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

//            Text::make('SKU', 'SKU')
//                ->rules(REQUIRED_STRING_VALIDATION),

            Number::make('Quantity')
                ->min(1),
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
