<?php

namespace App\Filament\Widgets\Embeded;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserStatusChartWidget extends ChartWidget
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
                    'data' => [$this->calculate(1), $this->calculate(0)],
                    'backgroundColor' => [
                        '#21B978FF',
                        '#21B978FF',
                    ],
                ],
            ],
            'labels' => ['active', 'inactive'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function calculate(int $val): int
    {
        return User::where('active', $val)->count();
    }
}
