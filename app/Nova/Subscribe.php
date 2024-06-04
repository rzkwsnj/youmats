<?php

namespace App\Nova;

use App\Nova\Metrics\SubscribesRevenue;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use ZakariaTlilani\NovaNestedTree\NestedTreeAttachManyField;

class Subscribe extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Subscribe::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    public static $tableStyle = 'tight';

    public static $polling = true;
    public static $pollingInterval = 30;
    public static $showPollingToggle = true;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'expiry_date'
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

            BelongsTo::make('Vendor', 'vendor', Vendor::class)
                ->rules([...REQUIRED_INTEGER_VALIDATION, ...['exists:vendors,id']])
                ->withoutTrashed()->searchable(),

            BelongsTo::make('Membership')
                ->rules([...REQUIRED_INTEGER_VALIDATION, ...['exists:memberships,id']])
                ->withoutTrashed(),

            BelongsTo::make('Category')->exceptOnForms(),
            NestedTreeAttachManyField::make('Category')
                ->rules([...REQUIRED_INTEGER_VALIDATION, ...['exists:categories,id']])
                ->useSingleSelect()->nullable(),

            Date::make('Subscribe Date', 'created_at')
                ->exceptOnForms(),

            Date::make('Expiry Date')
                ->rules(REQUIRED_DATE_VALIDATION),

            Currency::make('Price')
                ->rules(REQUIRED_NUMERIC_VALIDATION)->min(0)->step(0.05),
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request): bool
    {
        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request): bool
    {
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToDelete(Request $request): bool
    {
        return true;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [
            new SubscribesRevenue
        ];
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