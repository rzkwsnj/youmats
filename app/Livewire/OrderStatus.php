<?php

namespace App\Livewire;

use App\Helpers\Traits\NumericFormat;
use App\Models\Order;
use Livewire\Component;

class OrderStatus extends Component
{
    use NumericFormat;

    protected Order $orders;

    // 'pending','shipping','completed','refused'
    public ?int $totalPendingOrder;
    public ?int $totalShippingOrder;
    public ?int $totalCompleteOrder;
    public ?int $totalRefusedOrder;
    public ?int $totalOrders;

    public function mount(Order $orders): void
    {
        $this->orders = $orders;
    }

    public function render()
    {
        $this->totalOrders = $this->thousandsCurrencyFormat($this->orders->count());
        $this->totalPendingOrder = $this->thousandsCurrencyFormat($this->orders->where('status', 'pending')->count());
        $this->totalShippingOrder = $this->thousandsCurrencyFormat($this->orders->where('status', 'shipping')->count());
        $this->totalCompleteOrder = $this->thousandsCurrencyFormat($this->orders->where('status', 'completed')->count());
        $this->totalRefusedOrder = $this->thousandsCurrencyFormat($this->orders->where('status', 'refused')->count());

        return view('livewire.order-status', [
            'totalOrders' => $this->totalOrders,
            'totalPendingOrder' => $this->totalPendingOrder,
            'totalShippingOrder' => $this->totalShippingOrder,
            'totalCompleteOrder' => $this->totalCompleteOrder,
            'totalRefusedOrder' => $this->totalRefusedOrder,
        ]);
    }
}
