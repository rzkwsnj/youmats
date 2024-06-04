<?php

namespace App\Filament\Widgets\Embeded;

use App\Models\Quote;
use Filament\Widgets\ChartWidget;

class QuoteStatusChartWidget extends ChartWidget
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
                    'data' => [$this->calculate('pending'), $this->calculate('shipping'), $this->calculate('completed'), $this->calculate('refused')],
                    'backgroundColor' => [
                        '#FFED4AFF',
                        '#FFED4AFF',
                        '#FFED4AFF',
                        '#FFED4AFF',
                    ],
                ],
            ],
            'labels' => ['pending', 'shipping', 'completed', 'refused'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function calculate(string $val): int
    {
        return Quote::where('status', $val)->count();
    }
}
