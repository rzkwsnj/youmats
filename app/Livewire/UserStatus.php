<?php

namespace App\Livewire;

use App\Helpers\Traits\NumericFormat;
use App\Models\User;
use Livewire\Component;

class UserStatus extends Component
{
    use NumericFormat;

    protected User $users;
    public ?int $totalActiveUser;
    public ?int $totalUsers;

    public function mount(User $users): void
    {
        $this->users = $users;
    }

    public function render()
    {
        $this->totalActiveUser = $this->thousandsCurrencyFormat($this->users->where('active', 1)->count());
        $this->totalUsers = $this->thousandsCurrencyFormat($this->users->count());

        return view('livewire.user-status', [
            'totalActiveUser' => $this->totalActiveUser,
            'totalUsers' => $this->totalUsers,
        ]);
    }
}
