<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ErrorLog extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\ErrorLog::class;

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
        'id',
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

            Text::make('message', 'message', fn() => Str::limit($this->message , 65) )
            ->onlyOnIndex(),

            Text::make('message')
            ->sortable()
            ->hideFromIndex()
            ->rules(REQUIRED_TEXT_VALIDATION),

            Text::make('context')
            ->sortable()
            ->hideFromIndex(),

            Text::make('channel')
            ->sortable()
            ->hideFromIndex(),

            Text::make('extra')
            ->sortable()
            ->hideFromIndex(),

            Text::make('remote_addr')
            ->sortable()
            ->hideFromIndex(),

            Text::make('user_agent')
            ->sortable()
            ->hideFromIndex(),

            Text::make('counter')
            ->sortable(),

            Text::make('created_at')
            ->sortable()
            ->hideFromIndex(),

            Text::make('updated_at')
            ->sortable()
            ->hideFromIndex()

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
     * @param  \Illuminate\Http\NovaRequest  $request
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

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
}