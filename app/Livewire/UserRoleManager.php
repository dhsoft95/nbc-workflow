<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class UserRoleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $userId;
    public $selectedRoles = [];
    public $showEditModal = false;

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.user-role-manager', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function editUserRoles($userId)
    {
        $this->userId = $userId;
        $user = User::findOrFail($userId);
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showEditModal = true;
    }

    public function updateUserRoles()
    {
        try {
            $user = User::findOrFail($this->userId);
            $user->syncRoles($this->selectedRoles);
            $this->showEditModal = false;

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => "Roles updated successfully for {$user->name}.",
                'position' => 'top-right'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user roles: ' . $e->getMessage());

            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'Error updating roles: ' . $e->getMessage(),
                'position' => 'top-right'
            ]);
        }
    }

    #[On('refreshUsers')]
    public function refresh()
    {
        // Used to refresh the component from external emitters
    }

    public function closeModal()
    {
        $this->showEditModal = false;
    }
}
