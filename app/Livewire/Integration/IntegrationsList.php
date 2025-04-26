<?php

namespace App\Livewire\Integration;

use App\Models\Integration;
use Livewire\Component;
use Livewire\WithPagination;


class IntegrationsList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $type = '';
    public $priority = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Integration::query()
            ->with(['creator', 'internalIntegration', 'externalIntegration'])
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('purpose', 'like', '%' . $this->search . '%')
                    ->orWhere('department', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->type, function ($query) {
                return $query->where('type', $this->type);
            })
            ->when($this->priority, function ($query) {
                return $query->where('priority', $this->priority);
            })
            ->latest();

        return view('livewire.integration.integrations-list', [
            'integrations' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
