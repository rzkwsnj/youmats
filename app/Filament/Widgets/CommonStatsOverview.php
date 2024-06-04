<?php

namespace App\Filament\Widgets;

use App\Helpers\Traits\NumericFormat;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CommonStatsOverview extends BaseWidget
{
    use NumericFormat;

    protected static ?int $sort = 10;

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

    public function getColumns(): int
    {
        return 3;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Category Count', $this->thousandsCurrencyFormat(Category::count()))
                ->Icon('heroicon-m-chart-bar')
                ->description('Categories')
                ->color('info'),
            Stat::make('Product Count', $this->thousandsCurrencyFormat(Product::count()))
                ->Icon('heroicon-m-chart-bar')
                ->description('Products')
                ->color('info'),
            Stat::make('Subscriber Count', $this->thousandsCurrencyFormat(Subscriber::count()))
                ->Icon('heroicon-m-chart-bar')
                ->description('Subscribers')
                ->color('info'),
            Stat::make('Contact Count', $this->thousandsCurrencyFormat(Contact::count()))
                ->Icon('heroicon-m-chart-bar')
                ->description('Contacts')
                ->color('info'),
        ];
    }
}
