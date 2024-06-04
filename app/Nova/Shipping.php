<?php

namespace App\Nova;

use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaFlexibleContent\Flexible;

class Shipping extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Shipping::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
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

            BelongsTo::make('Vendor')
                ->searchable()->withoutTrashed(),

            Text::make('Name')->rules(REQUIRED_STRING_VALIDATION),

            new Panel('Shipping terms', [
                Flexible::make('Prices')
                    ->addLayout('Cars', 'cars', [
                        Text::make('Car Type', 'car_type')->rules(REQUIRED_STRING_VALIDATION),
                        Flexible::make('Cities')
                            ->addLayout('Cities', 'cities', [
                                Select::make('City')->options(function () {
                                    $collection = [];
                                    $data = \App\Models\City::with('country')->get();
                                    foreach ($data as $row) {
                                        $collection[$row->id] = ['label' => $row->name, 'group' => $row->country->name];
                                    }
                                    return $collection;
                                })->displayUsingLabels()->placeholder('Choose City')->rules(['required', 'integer']),
                                Number::make('Quantity')->rules(REQUIRED_INTEGER_VALIDATION)->min(1)->step(1),
                                Currency::make('Price')->rules(REQUIRED_NUMERIC_VALIDATION)->min(0)->step(0.05),
                                Number::make('Time')->rules(REQUIRED_INTEGER_VALIDATION)->min(1)->step(1),
                                Select::make('Format')->options(['hour' => 'Hour', 'day' => 'Day'])->rules(['required', 'in:hour,day']),
                            ]),
                    ])
            ])
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