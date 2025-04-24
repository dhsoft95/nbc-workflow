<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Collection;

class RolePermissionManager extends Component
{
    use WithPagination;

    public $roles;
    public $permissions;
    public $selectedRole;
    public $selectedPermissions = [];
    public $permissionSearch = '';

    // For role colors and icons
    protected $roleStyles = [
        'administrator' => [
            'color' => 'primary',
            'icon' => 'icon-shield'
        ],
        'requester' => [
            'color' => 'info',
            'icon' => 'icon-user'
        ],
        'app_owner' => [
            'color' => 'success',
            'icon' => 'icon-screen-desktop'
        ],
        'idi_team' => [
            'color' => 'warning',
            'icon' => 'icon-layers'
        ],
        'security_team' => [
            'color' => 'danger',
            'icon' => 'icon-lock'
        ],
        'infrastructure_team' => [
            'color' => 'secondary',
            'icon' => 'icon-wrench'
        ],
        'vendor_manager' => [
            'color' => 'dark',
            'icon' => 'icon-briefcase'
        ],
        'report_viewer' => [
            'color' => 'info',
            'icon' => 'icon-chart'
        ]
    ];

    // Listeners for events
    protected $listeners = ['refreshPermissions' => '$refresh'];

    public function mount()
    {
        $this->roles = Role::all();
        $this->permissions = Permission::all();
    }

    public function render()
    {
        $filteredPermissions = $this->permissions;

        // Always start with fresh permissions collection
        $allPermissions = Permission::all();

        // Filter permissions based on search term
        $filteredPermissions = !empty($this->permissionSearch)
            ? $allPermissions->filter(function ($permission) {
                $search = strtolower($this->permissionSearch);
                return strpos(strtolower($permission->name), $search) !== false;
            })
            : $allPermissions;

        return view('livewire.role-permission-manager', [
            'filteredPermissions' => $filteredPermissions,
            'roleStyles' => $this->roleStyles
        ]);
    }

    public function selectRole($roleId)
    {
        $this->selectedRole = Role::findById($roleId);
        $this->selectedPermissions = $this->selectedRole->permissions->pluck('id')->toArray();
        $this->permissionSearch = ''; // Reset search when changing roles
    }

    public function updateRolePermissions()
    {
        try {
            if (!$this->selectedRole) {
                $this->dispatch('showToastr', [
                    'type' => 'error',
                    'message' => 'Please select a role first!'
                ]);
                return;
            }

            $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
            $this->selectedRole->syncPermissions($permissions);

            $this->dispatch('showToastr', [
                'type' => 'success',
                'message' => 'Role permissions updated successfully!'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('showToastr', [
                'type' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function selectAllPermissions()
    {
        if ($this->selectedRole) {
            $this->selectedPermissions = $this->permissions->pluck('id')->toArray();
        }
    }

    public function deselectAllPermissions()
    {
        if ($this->selectedRole) {
            $this->selectedPermissions = [];
        }
    }

    public function getRoleStyle($roleName)
    {
        $name = strtolower($roleName);
        return $this->roleStyles[$name] ?? ['color' => 'primary', 'icon' => 'icon-tag'];
    }
}
