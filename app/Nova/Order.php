<?php

namespace App\Nova;

use App\Nova\Filters\Order\OrderDate;
use App\Nova\Filters\Order\OrderStatus;
use App\Nova\Filters\Order\PaymentMethod;
use App\Nova\Filters\Order\PaymentStatus;
use App\Nova\Metrics\OrdersPerDay;
use App\Nova\Metrics\OrdersStatus;
use App\Nova\Metrics\Revenue;
use Alexwenzel\DependencyContainer\DependencyContainer;
use Inspheric\Fields\Indicator;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Wdelfuego\Nova\DateTime\Fields\DateTime;
use YieldStudio\NovaPhoneField\PhoneNumber;

class Order extends Resource
{
    public static $model = \App\Models\Order::class;

    public static $title = 'order_id';

    public static $tableStyle = 'tight';

    public static $polling = true;
    public static $pollingInterval = 30;
    public static $showPollingToggle = true;

    public static $search = [
        'id', 'order_id', 'name', 'email'
    ];

    public static function relatableQuery(NovaRequest $request, $query) {
        return $query->where('type', 'individual');
    }

    public function fields(NovaRequest $request)
    {
        return [
            Text::make(__('ID'), 'id')->exceptOnForms()->sortable(),
            Text::make('Order Id', 'order_id', fn() =>
                '<a href="'. Nova::path()."/resources/{$this->uriKey()}/{$this->id}" . '" class="font-bold no-underline dim text-primary">'. $this->order_id . '</a>'
            )->asHtml()->exceptOnForms()->default('ORD'.strtoupper(uniqid())),

            DateTime::make('Date', 'created_at')->hideWhenCreating()->hideWhenUpdating()->sortable(),
            BelongsTo::make('User')->withoutTrashed()->hideFromIndex(),
            Text::make('Name')->sortable()->rules(REQUIRED_STRING_VALIDATION),
            Text::make('Email')->hideFromIndex()->rules(REQUIRED_EMAIL_VALIDATION),
            PhoneNumber::make('Phone')
                ->withCustomFormats('(+966) ###-###-###')
                ->rules(REQUIRED_STRING_VALIDATION),
            PhoneNumber::make('Phone2')
                ->withCustomFormats('(+966) ###-###-###')
                ->hideFromIndex()
                ->rules(REQUIRED_STRING_VALIDATION),
            Text::make('Address')->hideFromIndex()->rules(REQUIRED_STRING_VALIDATION),
            Text::make('Building Number')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
            Text::make('Street')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
            Text::make('District')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
            Text::make('City')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),

            (Panel::make('Payment Information', [
                Select::make('Payment Method')->options([
                    'cash' => 'Cash',
                    'credit card' => 'Credit Card',
                    'paypal' => 'PayPal'
                ])->default('cash')->displayUsingLabels()
                    ->rules(array_merge(REQUIRED_STRING_VALIDATION, ['In:cash,credit card,paypal'])),

                DependencyContainer::make([
                    Text::make('Reference Number')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Number')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Type')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Name')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    Text::make('Card Expire Date', 'card_exp_date')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                    DateTime::make('Transaction Date')->hideFromIndex()->rules(NULLABLE_STRING_VALIDATION),
                ])->dependsOnNot('payment_method', 'cash'),
            ])),

            Select::make('Payment Status')->options([
                'pending' => 'Pending',
                'refunded' => 'Refunded',
                'completed' => 'Completed'
            ])->default('pending')->hideFromIndex()->hideFromDetail()
                ->rules(array_merge(REQUIRED_STRING_VALIDATION, ['In:pending,refunded,completed'])),
            Indicator::make('Payment Status')->colors([
                'pending' => 'yellow',
                'refunded' => 'red',
                'completed' => 'green'
            ])->labels([
                'pending' => 'Pending',
                'refunded' => 'Refunded',
                'completed' => 'Completed'
            ]),

            Select::make('Order Status', 'status')->options([
                'pending' => 'Pending',
                'shipping' => 'Shipping',
                'completed' => 'Completed',
                'refused' => 'Refused'
            ])->default('pending')->hideFromIndex()->hideFromDetail()
                ->rules(array_merge(REQUIRED_STRING_VALIDATION, ['In:pending,shipping,completed,refused'])),
            Indicator::make('Order Status', 'status')->colors([
                'pending' => 'yellow',
                'shipping' => 'orange',
                'completed' => 'green',
                'refused' => 'red',
            ])->labels([
                'pending' => 'Pending',
                'shipping' => 'Shipping',
                'completed' => 'Completed',
                'refused' => 'Refused'
            ]),

            Textarea::make('Notes')->rules(NULLABLE_TEXT_VALIDATION),
            Textarea::make('Refused Notes')->rules(NULLABLE_TEXT_VALIDATION),

            Currency::make('Subtotal')->hideFromIndex()->rules(REQUIRED_NUMERIC_VALIDATION),
            Currency::make('Delivery')->hideFromIndex()->rules(REQUIRED_NUMERIC_VALIDATION),
            Currency::make('Total Price')->rules(REQUIRED_NUMERIC_VALIDATION),

            HasMany::make('Order Items', 'items'),

        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            new Revenue,
            new OrdersPerDay,
            new OrdersStatus
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new OrderDate,
            new PaymentStatus,
            new OrderStatus,
            new PaymentMethod
        ];
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
