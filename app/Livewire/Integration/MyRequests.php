<?php

namespace App\Livewire\Integration;
namespace App\Livewire\Integration;

use App\Models\Integration;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Integration::where('created_by', Auth::id())
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('purpose', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                return $query->where('status', $this->status);
            })
            ->latest();

        return view('livewire.integration.my-requests', [
            'integrations' => $query->paginate(10),
        ])->layout('layouts.app', ['title' => 'My Integration Requests']);
    }
}
