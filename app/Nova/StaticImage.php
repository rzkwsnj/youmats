<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class StaticImage extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\StaticImage::class;

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
    public static $search = [];

    public static $searchable = false;

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

            Fields::image(true, LOGO_PATH, 'Logo', true)->showOnIndex(),
            Fields::image(true, FAVICON_PATH, 'Favicon', true)->showOnIndex(),
            Fields::image(true, SLIDER_BACKGROUND_PATH, 'Slider Background', true)->showOnIndex(),
            Fields::image(true, HOME_FIRST_SECTION_PATH, 'Home First Section', true)->showOnIndex(),
            Fields::image(true, HOME_SECOND_SECTION_PATH, 'Home Second Section', true)->showOnIndex(),
            Fields::image(true, HOME_THIRD_SECTION_PATH, 'Home Third Section', true)->showOnIndex(),
            Fields::image(true, WHATSAPP_QR_CODE_PATH, 'Whatsapp QR code', true)->showOnIndex(),

        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
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
