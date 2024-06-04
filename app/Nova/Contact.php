<?php

namespace App\Nova;

use App\Nova\Filters\Contact\ContactDate;
use App\Nova\Metrics\ContactCount;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wdelfuego\Nova\DateTime\Fields\DateTime;
use YieldStudio\NovaPhoneField\PhoneNumber;

class Contact extends Resource
{
    public static $model = \App\Models\Contact::class;

    public static $title = 'email';

    public static $tableStyle = 'tight';

    public static $polling = true;
    public static $pollingInterval = 30;
    public static $showPollingToggle = true;

    public static $search = [
        'id', 'name', 'email', 'phone', 'message'
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

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

        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            new ContactCount
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new ContactDate
        ];
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
