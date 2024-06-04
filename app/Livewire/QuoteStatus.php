<?php

namespace App\Livewire;

use App\Helpers\Traits\NumericFormat;
use App\Models\Quote;
use Livewire\Component;

class QuoteStatus extends Component
{
    use NumericFormat;

    protected Quote $quotes;

    // 'pending','shipping','completed','refused'
    public ?int $totalPendingQuote;
    public ?int $totalShippingQuote;
    public ?int $totalCompleteQuote;
    public ?int $totalRefusedQuote;
    public ?int $totalQuotes;

    public function mount(Quote $quotes): void
    {
        $this->quotes = $quotes;
    }

    public function render()
    {
        $this->totalQuotes = $this->thousandsCurrencyFormat($this->quotes->count());
        $this->totalPendingQuote = $this->thousandsCurrencyFormat($this->quotes->where('status', 'pending')->count());
        $this->totalShippingQuote = $this->thousandsCurrencyFormat($this->quotes->where('status', 'shipping')->count());
        $this->totalCompleteQuote = $this->thousandsCurrencyFormat($this->quotes->where('status', 'completed')->count());
        $this->totalRefusedQuote = $this->thousandsCurrencyFormat($this->quotes->where('status', 'refused')->count());


        return view('livewire.quote-status', [
            'totalQuotes' => $this->totalQuotes,
            'totalPendingQuote' => $this->totalPendingQuote,
            'totalShippingQuote' => $this->totalShippingQuote,
            'totalCompleteQuote' => $this->totalCompleteQuote,
            'totalRefusedQuote' => $this->totalRefusedQuote,
        ]);
    }
}
