<div class="row">
    <!-- Roles Panel -->
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="icon-badge mr-2 text-primary"></i>
                    Roles
                </h5>
                <span class="badge badge-primary">{{ count($roles) }}</span>
            </div>
            <div class="card-body p-3">
                <div class="list-group">
                    @foreach ($roles as $role)
                        <a href="javascript:void(0)"
                           wire:click="selectRole({{ $role->id }})"
                           class="list-group-item list-group-item-action rounded mb-2
                               {{ $selectedRole && $selectedRole->id == $role->id
                                   ? 'active bg-primary text-white border-0'
                                   : 'border' }}">
                            @php
                                $roleStyle = $roleStyles[strtolower($role->name)] ?? ['color' => 'primary', 'icon' => 'icon-tag'];
                            @endphp
                            <i class="{{ $roleStyle['icon'] }} mr-2"></i> {{ ucfirst($role->name) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Panel -->
    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="icon-lock mr-2 text-warning"></i>
                    @if($selectedRole)
                        Permissions for <strong>{{ ucfirst($selectedRole->name) }}</strong>
                    @else
                        Select a role to manage permissions
                    @endif
                </h5>
                @if($selectedRole)
                    <span class="badge badge-pill badge-primary">{{ count($selectedPermissions) }} selected</span>
                @endif
            </div>
            <div class="card-body">
                @if($selectedRole)
                    <!-- Group permissions by category for better organization -->
                    @php
                        $groupedPermissions = $filteredPermissions->groupBy(function ($permission) {
                        $parts = explode('.', $permission->name);
                        return $parts[0] ?? 'general';
                        });
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-group mb-0 flex-grow-1 mr-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="icon-magnifier"></i>
                                    </span>
                                    </div>
                                    <input
                                        type="text"
                                        wire:model.debounce.300ms="permissionSearch"
                                        class="form-control border-right-0"
                                        placeholder="Search permissions..."
                                        aria-label="Search permissions">
                                    <div class="input-group-append">
                                    <span class="input-group-text bg-transparent border-left-0">
                                        <button
                                            class="btn btn-link p-0 text-muted"
                                            wire:click="$set('permissionSearch', '')"
                                            wire:target="permissionSearch"
                                            {{-- Show clear button only when search is active --}}
                                            style="visibility: {{ $permissionSearch ? 'visible' : 'hidden' }}">
                                            <i class="icon-close"></i>
                                        </button>
                                    </span>
                                    </div>
                                </div>
                                <div wire:loading.delay class="text-muted small mt-1">
                                    <i class="icon-loading animate-spin mr-1"></i> Searching...
                                </div>
                            </div>
                            <div>
                                <button
                                    wire:click="selectAllPermissions"
                                    class="btn btn-sm btn-outline-primary mr-2"
                                    title="Select all visible permissions">
                                    <i class="icon-check mr-1"></i> Select All
                                </button>
                                <button
                                    wire:click="deselectAllPermissions"
                                    class="btn btn-sm btn-outline-secondary"
                                    title="Deselect all permissions">
                                    <i class="icon-close mr-1"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="accordion" id="permissionsAccordion">
                        @foreach($groupedPermissions as $group => $permissionGroup)
                            <div class="card mb-2 border">
                                <div class="card-header bg-light p-2" id="heading{{ $group }}">
                                    <h2 class="mb-0">
                                        <button
                                            class="btn btn-link btn-block text-left text-decoration-none d-flex justify-content-between align-items-center"
                                            type="button" data-toggle="collapse" data-target="#collapse{{ $group }}"
                                            aria-expanded="true" aria-controls="collapse{{ $group }}">
                                    <span>
                                        <i class="icon-folder mr-2"></i> {{ ucfirst($group) }}
                                    </span>
                                            <span class="badge badge-primary">{{ count($permissionGroup) }}</span>
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapse{{ $group }}" class="collapse show"
                                     aria-labelledby="heading{{ $group }}" data-parent="#permissionsAccordion">
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($permissionGroup as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input
                                                            type="checkbox"
                                                            wire:model="selectedPermissions"
                                                            value="{{ $permission->id }}"
                                                            id="permission-{{ $permission->id }}"
                                                            class="custom-control-input">
                                                        <label class="custom-control-label"
                                                               for="permission-{{ $permission->id }}">
                                                            <i class="icon-key mr-1 text-muted"></i>
                                                            {{ str_replace("$group.", "", $permission->name) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-right mt-4">
                        <button
                            wire:click="updateRolePermissions"
                            class="btn btn-success px-4">
                            <i class="icon-check mr-1"></i> Update Permissions
                        </button>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="icon-cursor display-4"></i>
                        <p class="mt-3">Please select a role from the left panel to manage its permissions</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- This script ensures toast notifications work with session flashes -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Listen for session flash message events
        Livewire.on('flashMessage', message => {
            // Dispatch a custom event that the parent layout will listen for
            Livewire.dispatch('showToastr', {
                type: message.type,
                message: message.content
            });
        });
    });
    // Emit flash messages from session data
    @if(session() - > has('message'))
    Livewire.dispatch('showToastr', {
        type: 'success',
        message: "{{ session('message') }}"
    });
    @endif
    @if(session() - > has('error'))
    Livewire.dispatch('showToastr', {
        type: 'error',
        message: "{{ session('error') }}"
    });
    @endif
</script>
