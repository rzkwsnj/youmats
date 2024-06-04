<?php

namespace App\Livewire;

use App\Helpers\Traits\NumericFormat;
use App\Models\Order;
use Flowframe\Trend\Trend;
use Illuminate\Support\Collection;
use Livewire\Component;

class Revenue extends Component
{
    use NumericFormat;

    protected Order $orders;

    public ?string $totalRevenue;

    public ?int $period = 0;

    public function mount(Order $orders): void
    {
        $this->orders = $orders;
    }

    public function render()
    {
        $this->totalRevenue = $this->thousandsCurrencyFormat($this->calculate(intval($this->period)));

        return view('livewire.revenue', [
            'totalRevenue' => $this->totalRevenue,
        ]);
    }

    public function calculate(?int $val): int
    {
        if ($val == 0) {
            return Order::whereBetween('created_at', [now()->subWeeks(), now()])
                ->sum('total_price');
        } elseif ($val == 1) {
            return Order::where('created_at', now())
                ->sum('total_price');
        } else {
            return Order::whereBetween('created_at', [now()->subMonthsWithOverflow($val), now()])
                ->sum('total_price');
        }

    }
}
