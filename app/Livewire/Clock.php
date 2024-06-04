<?php

namespace App\Livewire;

use Carbon\Carbon;
use Livewire\Component;

class Clock extends Component
{
    public ?string $clock;
    public ?string $date;
    public ?string $tz = 'Asia/Riyadh';

    public function mount(): void
    {
        //
    }

    public function render()
    {
        $this->clock = Carbon::now()->timezone($this->tz)->format('H:i:s');
        $this->date = Carbon::now()->timezone($this->tz)->format('D, d M Y');

        return view('livewire.clock', [
            'clock' => $this->clock,
        ]);
    }
}
