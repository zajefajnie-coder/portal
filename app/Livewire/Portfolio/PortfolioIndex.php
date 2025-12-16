<?php

namespace App\Livewire\Portfolio;

use App\Models\Portfolio;
use Livewire\Component;
use Livewire\WithPagination;

class PortfolioIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $portfolios = Portfolio::with('user')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate(12);

        return view('livewire.portfolio.index', compact('portfolios'));
    }
}


