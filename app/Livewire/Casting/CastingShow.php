<?php

namespace App\Livewire\Casting;

use App\Models\Casting;
use Livewire\Component;

class CastingShow extends Component
{
    public Casting $casting;

    public function mount(Casting $casting)
    {
        $this->casting = $casting->load('user');
    }

    public function render()
    {
        return view('livewire.casting.show');
    }
}


