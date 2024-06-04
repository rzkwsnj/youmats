<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Davidpiesse\NovaToggle\Toggle;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Http\Requests\NovaRequest;

class Car extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Car::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'model';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'model', 'license_no'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            BelongsTo::make('Driver')
                ->showCreateRelationButton()
                ->withoutTrashed(),

            BelongsTo::make('Type', 'type', CarType::class)
                ->showCreateRelationButton()
                ->withoutTrashed()
                ->hideFromIndex(),

            Text::make('Name')
                ->sortable()
                ->translatable()
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Text::make(
                'Name',
                'name',
                fn () =>
                '<a href="' . Nova::path() . "/resources/{$this->uriKey()}/{$this->id}" . '" class="font-bold no-underline dim text-primary">' . $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            Text::make('Model')
                ->sortable()
                ->rules(REQUIRED_STRING_VALIDATION),

            Text::make('License No.', 'license_no')
                ->hideFromIndex()
                ->rules(REQUIRED_STRING_VALIDATION),

            Number::make('Maximum Load', 'max_load')
                ->hideFromIndex()
                ->rules(REQUIRED_INTEGER_VALIDATION)
                ->help('Weight / Tons'),

            Currency::make('Price', 'price_per_kilo')
                ->rules(REQUIRED_NUMERIC_VALIDATION)
                ->min(0)
                ->step(0.05)
                ->help('Price Per Kilo/Meter')
                ->hideFromIndex(),

            Toggle::make('Active'),

            Fields::image(false, CAR_PHOTO, 'Car Photo', false),

            Fields::file(false, CAR_LICENSE, 'Car License', false),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
