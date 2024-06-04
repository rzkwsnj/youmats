<?php

namespace App\Nova\Dashboards;

use App\Nova\Metrics\CategoryCount;
use App\Nova\Metrics\ContactCount;
use App\Nova\Metrics\OrdersPerDay;
use App\Nova\Metrics\OrdersStatus;
use App\Nova\Metrics\ProductCount;
use App\Nova\Metrics\Quote\QuotePerDay;
use App\Nova\Metrics\Quote\QuotesStatus;
use App\Nova\Metrics\Revenue;
use App\Nova\Metrics\SubscribersCount;
use App\Nova\Metrics\UsersPerDay;
use App\Nova\Metrics\UsersStatus;
use App\Nova\Metrics\UsersType;
use Laravel\Nova\Dashboards\Main as Dashboard;
use Richardkeep\NovaTimenow\NovaTimenow;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            (new NovaTimenow)->timezones([
                'Asia/Riyadh',
                'Africa/Cairo',
                'Asia/Dubai',
                'America/New_York',
                'Australia/Sydney',
                'Europe/Paris',
                'Europe/London',
            ])->defaultTimezone('Asia/Riyadh'),
            new UsersType,
            new UsersPerDay,
            new UsersStatus,
            new Revenue,
            new OrdersPerDay,
            new OrdersStatus,
            new QuotePerDay,
            new QuotesStatus,
            new CategoryCount,
            new ProductCount,
            new SubscribersCount,
            new ContactCount
        ];
    }
}