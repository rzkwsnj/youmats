<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Davidpiesse\NovaToggle\Toggle;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use YieldStudio\NovaPhoneField\PhoneNumber;

class Driver extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Driver::class;

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
        'id', 'name', 'email', 'phone', 'phone2', 'whatsapp'
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
            ID::make()->sortable(),

            BelongsTo::make('Country')
                ->showCreateRelationButton()
                ->withoutTrashed()
                ->hideFromIndex(),

            Text::make('Name')
                ->sortable()
                ->hideFromIndex()
                ->rules(REQUIRED_STRING_VALIDATION),

            Text::make(
                'Name',
                'name',
                fn () =>
                '<a href="' . Nova::path() . "/resources/{$this->uriKey()}/{$this->id}" . '" class="no-underline dim text-primary font-bold">' . $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            Text::make('Email')
                ->hideFromIndex()
                ->rules(REQUIRED_EMAIL_VALIDATION)
                ->creationRules('unique:drivers,email')
                ->updateRules('unique:drivers,email,{{resourceId}}'),

            PhoneNumber::make('Phone')
                ->withCustomFormats('(+966) ###-###-###')
                ->rules(REQUIRED_STRING_VALIDATION),

            PhoneNumber::make('Phone2')
                ->withCustomFormats('(+966) ###-###-###')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Text::make('Whatsapp')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Toggle::make('Active'),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->updateRules('nullable', 'string', 'min:8'),

            Fields::image(false, DRIVER_PHOTO, 'Driver Photo', true),

            Fields::file(false, DRIVER_ID, 'Driver ID', false),

            Fields::file(false, DRIVER_LICENSE, 'Driver License', false),

            HasOne::make('Car'),

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
