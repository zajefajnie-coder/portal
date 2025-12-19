<?php

namespace App\Livewire\Portfolio;

use App\Models\Portfolio;
use Livewire\Component;

class PortfolioShow extends Component
{
    public Portfolio $portfolio;

    public function mount(Portfolio $portfolio)
    {
        $this->portfolio = $portfolio->load('user');
    }

    public function render()
    {
        return view('livewire.portfolio.show');
    }
}


