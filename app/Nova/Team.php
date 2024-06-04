<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Davidpiesse\NovaToggle\Toggle;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Team extends Resource
{
    use HasSortableRows;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Team::class;

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
        'id', 'name', 'position'
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

            Fields::image(true, TEAM_PATH, 'Image', true),

            Text::make('Name')
                ->sortable()
                ->translatable()->hideFromIndex()
                ->rules(REQUIRED_STRING_VALIDATION),

            Text::make(
                'Name',
                'name',
                fn () =>
                '<a href="' . Nova::path() . "/resources/{$this->uriKey()}/{$this->id}" . '" class="no-underline dim text-primary font-bold">' . $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            Text::make('Position')
                ->sortable()
                ->translatable()
                ->rules(REQUIRED_STRING_VALIDATION),

            Textarea::make('Info')
                ->hideFromIndex()
                ->translatable()
                ->alwaysShow()
                ->rules(NULLABLE_TEXT_VALIDATION),

            Text::make('Facebook')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Text::make('Twitter')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Text::make('Gmail')
                ->hideFromIndex()
                ->rules(NULLABLE_STRING_VALIDATION),

            Toggle::make('Active'),
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
