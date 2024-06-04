<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class OrderStatusWidget extends Widget
{
    protected static ?int $sort = 7;

    protected static string $view = 'filament.widgets.order-status-widget';
}
