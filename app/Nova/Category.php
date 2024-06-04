<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use App\Nova\Actions\GenerateSitemap;
use App\Nova\Filters\Category\CategoryType;
use Clevyr\NovaFields\ActionButton;
use Davidpiesse\NovaToggle\Toggle;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Mostafaznv\NovaCkEditor\CkEditor;
use Outl1ne\NovaSimpleRepeatable\SimpleRepeatable;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class Category extends Resource
{
    use HasSortableRows;

    public static $model = \App\Models\Category::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'title', 'desc', 'short_desc'
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make('Name')
                ->hideFromIndex()
                ->translatable()
                ->rules(REQUIRED_STRING_VALIDATION),

            Text::make(
                'Name',
                'name',
                fn () =>
                '<a href="' . Nova::path() . "/resources/{$this->uriKey()}/{$this->id}" . '" class="font-bold no-underline dim text-primary">' . $this->name . '</a>'
            )->asHtml()->onlyOnIndex(),

            Text::make('H1 Title', 'title')->hideFromIndex()->translatable()->rules(NULLABLE_STRING_VALIDATION),

            Boolean::make('Category')->hideFromIndex(),

            BelongsTo::make('Parent', 'parent', self::class)->onlyOnIndex(),

            BelongsTo::make('Parent', 'parent', self::class)->nullable()->hideFromIndex(),

            CkEditor::make('Description', 'desc')
                ->hideFromIndex()
                ->translatable()
                ->rules(NULLABLE_TEXT_VALIDATION),

            Fields::image(true, CATEGORY_PATH, 'Image', true),

            Fields::image(true, CATEGORY_COVER, 'Cover', true),

            Toggle::make(__('Featured Sections'), 'featured_sections')->sortable(),
            Number::make(__('Featured Section Order'), 'featured_section_order')
                ->min(0)->step(1)->hideFromIndex(),

            Toggle::make(__('Featured'), 'isFeatured')->sortable(),
            Toggle::make(__('Top Category'), 'topCategory')->sortable(),
            Toggle::make(__('Show in footer'), 'show_in_footer')->sortable(),
            Toggle::make(__('Hide Availability'), 'hide_availability')->hideFromIndex(),
            Toggle::make(__('Hide Delivery Status'), 'hide_delivery_status')->hideFromIndex(),
            Toggle::make(__('Show Contact Widgets'), 'contact_widgets')->hideFromIndex(),
            Toggle::make(__('Show Warning'), 'show_warning')->hideFromIndex(),

            ActionButton::make('Sitemap')
                ->action(GenerateSitemap::class, $this->id)
                ->showLoadingAnimation()
                ->exceptOnForms(),

            new Panel('Template For Title', [
                Heading::make('Instructions: + => for input, - => for dropdown, Ex for dropdown: -Orientation-Horizontal-Vertical'),
                SimpleRepeatable::make('Template', 'template', [
                    Text::make('Word')->rules(NULLABLE_TEXT_VALIDATION)->translatable(),
                ])->canAddRows(true)->canDeleteRows(true),
            ]),

            Fields::SEO(static::$model, 'categories', true, false, true),

            HasMany::make('Children', 'children', self::class),
            HasMany::make('Products'),
            HasMany::make('Direct Products', 'allProducts', Product::class)
                ->showOnDetail(function () {
                    return $this->parent_id == NULL;
                }),
            HasMany::make('Attributes'),
            HasMany::make('Vendors'),
            BelongsToMany::make('Memberships'),
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
        return [
            new CategoryType
        ];
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
        return [
            (new GenerateSitemap)
                ->confirmText('Are you sure you want to generate sitemap for this category?')
                ->confirmButtonText('Generate')
                ->cancelButtonText("Don't generate"),
        ];
    }
}
