<?php

namespace App\Livewire\Casting;

use App\Models\Casting;
use Livewire\Component;
use Livewire\WithPagination;

class CastingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'open';
    public $filterRole = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    public function render()
    {
        $castings = Casting::with('user')
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterRole, function ($query) {
                $query->whereJsonContains('required_roles', $this->filterRole);
            })
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('location', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.casting.index', compact('castings'));
    }
}


