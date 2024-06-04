<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Davidpiesse\NovaToggle\Toggle;
use DmitryBubyakin\NovaMedialibraryField\Fields\Medialibrary;
use Laravel\Nova\Nova;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Mostafaznv\NovaCkEditor\CkEditor;
use ZakariaTlilani\NovaNestedTree\NestedTreeAttachManyField;

class SubCategory extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Category::class;

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

            Text::make('Name')
                ->hideFromIndex()->translatable()->rules(REQUIRED_STRING_VALIDATION),

            Text::make(
                'Name',
                'name',
                fn () =>
                '<a href="' . Nova::path() . "/resources/{$this->uriKey()}/{$this->id}" . '" class="no-underline dim text-primary font-bold">' . $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            BelongsTo::make('Parent', 'parent', self::class)->onlyOnIndex(),
            NestedTreeAttachManyField::make('Parent', 'parent', self::class)
                ->useSingleSelect()->hideFromIndex()->nullable(),
            Textarea::make('Short Description', 'short_desc')
                ->translatable()
                ->rules(NULLABLE_TEXT_VALIDATION),

            CkEditor::make('Description', 'desc')
                ->hideFromIndex()
                ->translatable()
                ->rules(REQUIRED_TEXT_VALIDATION),

            Medialibrary::make('Image', CATEGORY_PATH)->fields(function () {
                return [
                    Text::make('File Name', 'file_name')->rules('required', 'min:2'),
                    Text::make('Image Title', 'img_title')->translatable()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Image Alt', 'img_alt')->translatable()->rules(NULLABLE_STRING_VALIDATION)
                ];
            })->attachRules(REQUIRED_IMAGE_VALIDATION)
                ->accept('image/*')->autouploading()->attachOnDetails()->single()
                ->croppable('cropper'),

            Medialibrary::make('Cover', CATEGORY_COVER)->fields(function () {
                return [
                    Text::make('File Name', 'file_name')->rules('required', 'min:2'),
                    Text::make('Image Title', 'img_title')->translatable()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Image Alt', 'img_alt')->translatable()->rules(NULLABLE_STRING_VALIDATION)
                ];
            })->attachRules(REQUIRED_IMAGE_VALIDATION)
                ->accept('image/*')->autouploading()->attachOnDetails()->single()
                ->croppable('cropper')
                ->hideFromIndex(),

            Toggle::make(__('Featured'), 'isFeatured'),
            Toggle::make(__('Top Category'), 'topCategory'),
            Toggle::make(__('Show in footer'), 'show_in_footer'),

            Fields::SEO(static::$model, 'categories'),

            HasMany::make('Children', 'children', self::class),
            HasMany::make('Products'),
            HasMany::make('Attributes'),
            HasMany::make('Vendors'),
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
