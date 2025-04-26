<?php

namespace App\Livewire\Integration;

use App\Models\Integration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class PendingApprovals extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Map user roles to approval stages
        $stageMap = [
            'app_owner' => 'submitted', // Changed from 'app_owner_approval' to 'submitted'
            'idi_team' => 'app_owner_approval', // This should match when app_owner approves and it goes to IDI
            'security_team' => 'idi_approval',
            'infrastructure_team' => 'security_approval',
        ];

        // Get user's approval roles
        $approvalStages = [];
        foreach ($stageMap as $role => $stage) {
            if ($user->hasRole($role)) {
                $approvalStages[] = $stage;
            }
        }

        // Log for debugging
        Log::info('User roles and approval stages', [
            'user' => $user->name,
            'roles' => $user->getRoleNames(),
            'approvalStages' => $approvalStages
        ]);

        // Query integrations pending user's approval
        $query = Integration::whereIn('status', $approvalStages)
            ->when($this->search, function ($query) {
                return $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('purpose', 'like', '%' . $this->search . '%')
                        ->orWhere('department', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->type, function ($query) {
                return $query->where('type', $this->type);
            })
            ->latest();

        // Log the SQL query for debugging
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        Log::info('Pending approvals query', ['sql' => $sql, 'bindings' => $bindings]);

        $integrations = $query->paginate(10);

        Log::info('Pending integrations count', ['count' => $integrations->total()]);

        return view('livewire.integration.pending-approvals', [
            'integrations' => $integrations,
        ])->layout('layouts.app', ['title' => 'Pending Approvals']);
    }
}
