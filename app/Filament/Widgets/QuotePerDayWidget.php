<?php

namespace App\Filament\Widgets;

use App\Models\Quote;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Collection;

class QuotePerDayWidget extends ChartWidget
{
    protected static ?int $sort = 8;

    protected static ?string $heading = 'Quote Per Day';

    public ?string $filter = '0';

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
        $data =  $this->calculate(intval($this->filter));

        return [
            'datasets' => [
                [
                    'label' => '',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function calculate(int $val): Collection
    {
        return Trend::model(Quote::class)
            ->between(
                start: $val == 0 ? now()->subWeeks() : now()->subMonthsWithOverflow($val),
                end: now(),
            )
            ->perDay()
            ->count();
    }
}
