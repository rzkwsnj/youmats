<?php

namespace App\Filament\Widgets\Embeded;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserTypeChartWidget extends ChartWidget
{
    protected static ?string $heading = '';

    protected static ?string $pollingInterval = '10s';

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
        'scales' => [
            'x' => [
                'ticks' => [
                    'display' => false,
                ],
            ],
            'y' => [
                'ticks' => [
                    'display' => false,
                ],
                'grid' => [
                    'display' => false,
                ],
            ],
        ],
    ];

    public static function canView(): bool
    {
        return false;
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => '',
                    'data' => [$this->calculate('individual'), $this->calculate('company')],
                    'backgroundColor' => [
                        '#4099DEFF',
                        '#F99037FF'
                    ],
                ],
            ],
            'labels' => ['individual', 'company'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function calculate(string $val): int
    {
        return User::where('type', $val)->count();
    }
}
