<?php

namespace App\Nova;

use App\Nova\Actions\ImportSubscribers;
use App\Nova\Metrics\SubscribersCount;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;

class Subscriber extends Resource
{
    public static $model = \App\Models\Subscriber::class;

    public static $title = 'email';

    public static $tableStyle = 'tight';

    public static $search = [
        'id', 'email'
    ];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make('Email')
                ->sortable()
                ->rules(REQUIRED_EMAIL_VALIDATION)
                ->creationRules('unique:subscribers,email')
                ->updateRules('unique:subscribers,email,{{resourceId}}'),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            new SubscribersCount
        ];
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
        return [
            DownloadExcel::make()
                ->standalone()
                ->withHeadings('#', 'E-mail', 'Created at')
                ->only('id', 'email', 'created_at')
        ];
    }
}
