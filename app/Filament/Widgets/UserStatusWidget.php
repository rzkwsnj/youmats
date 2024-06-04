<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UserStatusWidget extends Widget
{
    protected static ?int $sort = 4;

    protected static string $view = 'filament.widgets.user-status-widget';
}
