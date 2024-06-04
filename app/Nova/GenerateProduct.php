<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use App\Nova\Actions\GenerateProductsAction;
use App\Nova\Actions\GenerateProductsTestAction;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Mostafaznv\NovaCkEditor\CkEditor;
use Maher\GenerateProducts\GenerateProducts;
use Clevyr\NovaFields\ActionButton;

class GenerateProduct extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\GenerateProduct::class;

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
     * @var bool
     */
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

            BelongsTo::make('Category'),

            BelongsTo::make('Vendor')->withoutTrashed()->searchable(),

            GenerateProducts::make('Template')
                ->category('category')
                ->endpoint('/api/loadData/{category}/model/{model}')
                ->onlyOnForms(),

            ActionButton::make('Generate')
                ->action(GenerateProductsAction::class, $this->id)
                ->text('Generate')
                ->exceptOnForms()
                ->showLoadingAnimation(),

            ActionButton::make('Test Generate')
                ->action(GenerateProductsTestAction::class, $this->id)
                ->text('Test')
                ->exceptOnForms()
                ->showLoadingAnimation(),

            CkEditor::make('Short Description', 'short_desc')
                ->hideFromIndex()->translatable()
                ->rules(REQUIRED_TEXT_VALIDATION),

            CkEditor::make('Description', 'desc')
                ->hideFromIndex()->translatable()
                ->rules(REQUIRED_TEXT_VALIDATION),

            (new Panel('Images', [
                Fields::image(false, GENERATE_PRODUCT_PATH, 'Images', false),
            ])),

            new Panel('Search Keywords', [
                Heading::make("Instructions: Set every keyword in one line"),
                Textarea::make('Search Keywords')
                    ->rules(NULLABLE_TEXT_VALIDATION)
                    ->translatable(),
            ]),

        ];
    }


    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
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
        return [
            (new GenerateProductsAction)
                ->confirmText('Are you sure you want to generate this products?')
                ->confirmButtonText('Generate')
                ->cancelButtonText("Don't generate"),
            new GenerateProductsTestAction
        ];
    }
}
