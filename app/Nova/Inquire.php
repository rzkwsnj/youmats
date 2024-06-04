<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wdelfuego\Nova\DateTime\Fields\DateTime;
use YieldStudio\NovaPhoneField\PhoneNumber;

class Inquire extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Inquire::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'email';

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
        'id', 'company_name', 'name', 'email', 'phone', 'message'
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

            Text::make('Company Name')
                ->sortable()
                ->rules(REQUIRED_STRING_VALIDATION),

            Text::make('Name')
                ->sortable()
                ->rules(REQUIRED_STRING_VALIDATION),

            DateTime::make('Date', 'created_at')->hideWhenCreating()->hideWhenUpdating()->sortable(),

            Text::make('Email')
                ->sortable()
                ->rules(REQUIRED_EMAIL_VALIDATION),

            PhoneNumber::make('Phone')
                ->withCustomFormats('(+966) ###-###-###')
                ->sortable()
                ->rules(REQUIRED_STRING_VALIDATION),

            Textarea::make('Message')
                ->hideFromIndex()
                ->rules(NULLABLE_TEXT_VALIDATION)
                ->alwaysShow(),

            Fields::file(false, INQUIRE_PATH, 'File', true)

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
