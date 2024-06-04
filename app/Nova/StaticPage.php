<?php

namespace App\Nova;

use App\Helpers\Nova\Fields;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Mostafaznv\NovaCkEditor\CkEditor;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class StaticPage extends Resource
{
    use HasSortableRows;

    public static $model = \App\Models\StaticPage::class;

    public static $title = 'title';

    public static $search = [
        'id', 'title', 'desc'
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make('Title')
                ->sortable()
                ->translatable()
                ->rules(REQUIRED_STRING_VALIDATION),

            Fields::image(true, PAGE_PATH, 'Image', true),

            CkEditor::make('Description', 'desc')
                ->hideFromIndex()
                ->translatable()
                ->rules(NULLABLE_TEXT_VALIDATION),

            Fields::SEO(static::$model, 'pages'),

        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [];
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
