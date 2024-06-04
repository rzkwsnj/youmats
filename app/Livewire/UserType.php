<?php

namespace App\Livewire;

use App\Helpers\Traits\NumericFormat;
use App\Models\User;
use Livewire\Component;

class UserType extends Component
{
    use NumericFormat;

    protected User $users;

    public ?int $totalIndividualUser;
    public ?int $totalCompanyUser;
    public ?int $totalUsers;

    public function mount(User $users): void
    {
        $this->users = $users;
    }

    public function render()
    {
        $this->totalIndividualUser = $this->thousandsCurrencyFormat($this->users->where('type', 'individual')->count());
        $this->totalCompanyUser = $this->thousandsCurrencyFormat($this->users->where('type', 'company')->count());
        $this->totalUsers = $this->thousandsCurrencyFormat($this->users->count());

        return view('livewire.user-type', [
            'totalIndividualUser' => $this->totalIndividualUser,
            'totalCompanyUser' => $this->totalCompanyUser,
            'totalUsers' => $this->totalUsers,
        ]);
    }
}
