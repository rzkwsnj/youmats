<?php

namespace App\Nova;

use Laravel\Nova\Nova;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class City extends Resource
{
    public static $model = \App\Models\City::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name'
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('Country')
                ->showCreateRelationButton()
                ->withoutTrashed(),

            Text::make('Name')
                ->sortable()
                ->translatable()
                ->hideFromIndex()
                ->rules(REQUIRED_STRING_VALIDATION),

            Text::make('Name', 'name', fn() =>
                '<a href="'. Nova::path()."/resources/{$this->uriKey()}/{$this->id}" . '" class="font-bold no-underline dim text-primary">'. $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            HasMany::make('Vendors'),
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