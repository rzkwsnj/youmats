<?php

namespace App\Nova;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wamesk\RatingField\RatingField;
use Wdelfuego\Nova\DateTime\Fields\DateTime;

class Trip extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Trip::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id'
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

            BelongsTo::make('User')->withoutTrashed(),
            BelongsTo::make('Driver')->withoutTrashed()->nullable(),

            Number::make('Distance')
                ->rules(REQUIRED_NUMERIC_VALIDATION)
                ->min(0)
                ->step(0.01)
                ->help('In KiloMeter'),

            Currency::make('Price')
                ->rules(NULLABLE_NUMERIC_VALIDATION)
                ->min(0)
                ->step(0.05),

            Select::make('Driver Status')->options([
                '0' => 'Pending',
                '1' => 'Accepted'
            ])->displayUsingLabels()
                ->rules(array_merge(REQUIRED_STRING_VALIDATION, ['In:0,1'])),

            Select::make('Status')->options([
                '0' => 'Pending',
                '1' => 'In progress',
                '2' => 'Completed',
                '3' => 'Canceled'
            ])->displayUsingLabels()
                ->rules(array_merge(REQUIRED_STRING_VALIDATION, ['In:0,1,2,3'])),

            DateTime::make('Pickup Date')
                ->nullable()
                ->hideFromIndex(),

            DateTime::make('Started At')
                ->nullable()
                ->hideFromIndex(),
                
            RatingField::make('User Rate')
                ->sizeStars(30)
                ->animate('true')
                ->maxRating(5)
                ->step(1)
                ->showNumber(false)
                ->hideFromIndex()
                ->rules(NULLABLE_NUMERIC_VALIDATION),
            
            Textarea::make('User Review')
                ->rules(NULLABLE_TEXT_VALIDATION)
                ->hideFromIndex(),

            RatingField::make('Driver Rate')
                ->sizeStars(30)
                ->animate('true')
                ->maxRating(5)
                ->step(1)
                ->showNumber(false)
                ->hideFromIndex()
                ->rules(NULLABLE_NUMERIC_VALIDATION),

            Textarea::make('Driver Review')
                ->rules(NULLABLE_TEXT_VALIDATION)
                ->hideFromIndex(),
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