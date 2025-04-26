<div>
    <div class="mb-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search users..."
               class="form-control">
    </div>
    <div class="card">
        <div class="header">
            <h2>User Management</h2>
        </div>
        <div class="body">
            <div class="table-responsive">
                <table class="table table-hover mb-0 c_list">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Current Roles</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department ?? 'N/A' }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    <span class="badge badge-info">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                <button wire:click="editUserRoles({{ $user->id }})"
                                         title="Edit Roles" style="background-color: #152755; color: white;" class="btn">
                                    <i class="fa fa-edit"></i> Edit Roles
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <!-- Modal for editing roles -->
    @if($showEditModal)
        <div class="modal fade show" style="display: block; z-index: 1050;">
            <div class="modal-dialog" style="z-index: 1060;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User Roles</h5>
                        <button type="button" class="close" wire:click="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @foreach ($roles as $role)
                            <div class="form-group">
                                <label class="fancy-checkbox">
                                    <input type="checkbox"
                                           wire:model="selectedRoles"
                                           value="{{ $role->name }}"
                                           id="role-{{ $role->id }}">
                                    <span></span>
                                    {{ ucfirst($role->name) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closeModal" type="button" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button wire:click="updateUserRoles" type="button" class="btn btn-primary">
                            Save
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        </div>
    @endif
</div>
