<div>
    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-blue text-dark d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User Management</h5>
            <div>
                <button wire:click="openModal()" class="btn btn-sm btn-light">
                    <i class="fa fa-plus mr-1"></i> Add New User
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Search Box -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                </div>
                <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Search users...">
            </div>

            <!-- Users Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Roles</th>
                        <th width="180px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->department ?? 'N/A' }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <button wire:click="openViewModal({{ $user->id }})" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button wire:click="openEditModal({{ $user->id }})" class="btn btn-sm btn-blue" style="background-color: #152755; color: white;">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button wire:click="openDeleteModal({{ $user->id }})" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No users found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($isOpen)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #152755;">
                        <h5 class="modal-title">{{ $user_id ? 'Edit User' : 'Create New User' }}</h5>
                        <button type="button" class="close text-white" wire:click="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" wire:model="name" class="form-control" placeholder="Enter name">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" wire:model="email" class="form-control" placeholder="Enter email">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <input type="text" wire:model="department" class="form-control" placeholder="Enter department">
                                @error('department') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Password {{ $user_id ? '(Leave blank to keep current password)' : '' }}</label>
                                <input type="password" wire:model="password" class="form-control" placeholder="Enter password">
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" wire:model="password_confirmation" class="form-control" placeholder="Confirm password">
                            </div>

                            <div class="form-group">
                                <label>Roles</label>
                                <div class="border p-2 rounded">
                                    @foreach($roles as $role)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="selectedRoles"
                                                   value="{{ $role->id }}"
                                                   id="role-{{ $role->id }}">
                                            <label class="form-check-label" for="role-{{ $role->id }}">
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedRoles') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-blue" style="background-color: #152755; color: white;">{{ $user_id ? 'Update' : 'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($isDeleteModalOpen)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="close text-white" wire:click="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancel</button>
                        <button type="button" wire:click="delete" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- View User Modal -->
    @if($isViewModalOpen && $currentUser)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">User Details</h5>
                        <button type="button" class="close text-white" wire:click="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $currentUser->name }}</h5>
                                <p class="card-text"><strong>Email:</strong> {{ $currentUser->email }}</p>
                                <p class="card-text"><strong>Department:</strong> {{ $currentUser->department ?? 'N/A' }}</p>
                                <p class="card-text"><strong>Roles:</strong></p>
                                <div>
                                    @foreach($currentUser->roles as $role)
                                        <span class="badge badge-info">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
