<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $department;
    public $user_id;
    public $selectedRoles = [];

    public $isOpen = false;
    public $isDeleteModalOpen = false;
    public $isViewModalOpen = false;
    public $search = '';
    public $currentUser;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'department' => 'nullable',
        'selectedRoles' => 'array',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';
        $users = User::where('name', 'like', $searchTerm)
            ->orWhere('email', 'like', $searchTerm)
            ->orWhere('department', 'like', $searchTerm)
            ->paginate(10);

        $roles = Role::all();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function openEditModal($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->department = $user->department;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->resetErrorBag();
        $this->isOpen = true;
    }

    public function openDeleteModal($id)
    {
        $this->user_id = $id;
        $this->isDeleteModalOpen = true;
    }

    public function openViewModal($id)
    {
        $this->currentUser = User::with('roles')->findOrFail($id);
        $this->isViewModalOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->isDeleteModalOpen = false;
        $this->isViewModalOpen = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->department = '';
        $this->user_id = '';
        $this->selectedRoles = [];
        $this->resetErrorBag();
    }

    public function store()
    {
        if ($this->user_id) {
            $this->validate([
                'name' => 'required|min:3',
                'email' => 'required|email|unique:users,email,' . $this->user_id,
                'password' => 'nullable|min:6|confirmed',
                'department' => 'nullable',
                'selectedRoles' => 'array',
            ]);

            $user = User::find($this->user_id);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'department' => $this->department,
            ]);

            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }

            // Sync roles
            $user->syncRoles($this->selectedRoles);

            session()->flash('message', 'User updated successfully.');
        } else {
            $this->validate();

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'department' => $this->department,
            ]);

            // Assign roles
            if (!empty($this->selectedRoles)) {
                $user->assignRole($this->selectedRoles);
            }

            session()->flash('message', 'User created successfully.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        if ($this->user_id) {
            $user = User::find($this->user_id);

            // Check if the user is trying to delete themselves
            if (auth()->id() === $user->id) {
                session()->flash('error', 'You cannot delete your own account!');
                $this->closeModal();
                return;
            }

            $user->delete();
            session()->flash('message', 'User deleted successfully.');
        }

        $this->closeModal();
    }
}
