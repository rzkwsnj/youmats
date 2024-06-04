<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Benjacho\BelongsToManyField\BelongsToManyField;
use Davidpiesse\NovaToggle\Toggle;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Mostafaznv\NovaCkEditor\CkEditor;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Article extends Resource
{
    use HasSortableRows;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Article::class;

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
        'name', 'slug'
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

            Text::make('Name')->hideFromIndex()->translatable()->rules(REQUIRED_STRING_VALIDATION),

            Text::make(
                'Name',
                'name',
                fn () =>
                '<a href="' . Nova::path() . "/resources/{$this->uriKey()}/{$this->id}" . '" class="font-bold no-underline dim text-primary">' . $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            BelongsToManyField::make('Tags')
                ->optionsLabel('translated_name')->hideFromIndex(),

            BelongsToManyField::make('Vendors')
                ->optionsLabel('translated_name')->hideFromIndex(),

            BelongsToManyField::make('Categories')
                ->optionsLabel('translated_name')->hideFromIndex(),

            CkEditor::make('Short Description', 'short_desc')
                ->hideFromIndex()->translatable()
                ->rules(NULLABLE_TEXT_VALIDATION),

            CkEditor::make('Description', 'desc')
                ->hideFromIndex()->translatable()
                ->rules(REQUIRED_TEXT_VALIDATION),

            Fields::image(true, ARTICLE_PATH, 'Image', true),

            Toggle::make('Active'),

            Fields::SEO(static::$model, 'articles'),

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
