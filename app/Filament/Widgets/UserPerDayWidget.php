<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Collection;

class UserPerDayWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Users Per Day';
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
    public ?string $filter = '0';

    protected function getFilters(): ?array
    {
        return [
            '0' => '7 Days',
            '1' => '30 Days',
            '2' => '60 Days',
            '3' => '90 Days',
        ];
    }

    protected function getData(): array
    {
        $data = $this->calculate(intval($this->filter));

        return [
            'datasets' => [
                [
                    'label' => '',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function calculate(int $val): Collection
    {
        return Trend::model(User::class)
            ->between(
                start: $val == 0 ? now()->subWeeks() : now()->subMonthsWithOverflow($val),
                end: now(),
            )
            ->perDay()
            ->count();
    }

    protected function getType(): string
    {
        return 'line';
    }
}
