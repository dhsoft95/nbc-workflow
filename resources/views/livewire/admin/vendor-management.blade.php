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
        <div class="card-header" style="background-color: #152755; color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Vendor Management</h5>
                <button wire:click="openModal()" class="btn btn-sm btn-light">
                    <i class="fa fa-plus"></i> Add New Vendor
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Search Box -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                </div>
                <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Search vendors...">
            </div>

            <!-- Vendors Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                            Name
                            @if($sortField === 'name')
                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('contact_email')" style="cursor: pointer;">
                            Email
                            @if($sortField === 'contact_email')
                                <i class="fa fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </th>
                        <th>Phone</th>
                        <th>Website</th>
                        <th width="180px">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->contact_email }}</td>
                            <td>{{ $vendor->contact_phone ?? 'N/A' }}</td>
                            <td>
                                @if($vendor->website)
                                    <a href="{{ $vendor->website }}" target="_blank">{{ $vendor->website }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <button wire:click="openViewModal({{ $vendor->id }})" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button wire:click="openEditModal({{ $vendor->id }})" class="btn btn-sm btn-blue" style="background-color: #007bff; color: white;">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button wire:click="openDeleteModal({{ $vendor->id }})" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No vendors found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($isOpen)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #007bff; color: white;">
                        <h5 class="modal-title">{{ $vendor_id ? 'Edit Vendor' : 'Create New Vendor' }}</h5>
                        <button type="button" class="close text-white" wire:click="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Vendor Name</label>
                                        <input type="text" wire:model="name" class="form-control" placeholder="Enter vendor name">
                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Email</label>
                                        <input type="email" wire:model="contact_email" class="form-control" placeholder="Enter contact email">
                                        @error('contact_email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Phone</label>
                                        <input type="text" wire:model="contact_phone" class="form-control" placeholder="Enter contact phone">
                                        @error('contact_phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Website</label>
                                        <input type="url" wire:model="website" class="form-control" placeholder="Enter website URL">
                                        @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea wire:model="address" class="form-control" rows="2" placeholder="Enter address"></textarea>
                                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea wire:model="description" class="form-control" rows="3" placeholder="Enter vendor description"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-blue" style="background-color: #007bff; color: white;">
                                {{ $vendor_id ? 'Update' : 'Save' }}
                            </button>
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
                        <p>Are you sure you want to delete this vendor? This action can be undone later.</p>
                        <p class="text-danger">Note: Vendors with associated integrations cannot be deleted.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancel</button>
                        <button type="button" wire:click="delete" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- View Vendor Modal -->
    @if($isViewModalOpen && $currentVendor)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Vendor Details</h5>
                        <button type="button" class="close text-white" wire:click="closeModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>{{ $currentVendor->name }}</h5>
                                <p>
                                    <i class="fa fa-envelope text-blue"></i>
                                    <a href="mailto:{{ $currentVendor->contact_email }}">{{ $currentVendor->contact_email }}</a>
                                </p>
                                @if($currentVendor->contact_phone)
                                    <p>
                                        <i class="fa fa-phone text-blue"></i>
                                        {{ $currentVendor->contact_phone }}
                                    </p>
                                @endif
                                @if($currentVendor->website)
                                    <p>
                                        <i class="fa fa-globe text-blue"></i>
                                        <a href="{{ $currentVendor->website }}" target="_blank">{{ $currentVendor->website }}</a>
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($currentVendor->address)
                                    <p>
                                        <strong>Address:</strong><br>
                                        {{ $currentVendor->address }}
                                    </p>
                                @endif
                                @if($currentVendor->description)
                                    <p>
                                        <strong>Description:</strong><br>
                                        {{ $currentVendor->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">Associated Integrations</h6>
                        @if($currentVendor->externalIntegrations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($currentVendor->externalIntegrations as $integration)
                                        <tr>
                                            <td>{{ $integration->name }}</td>
                                            <td>
                                                    <span class="badge badge-{{ $integration->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($integration->status) }}
                                                    </span>
                                            </td>
                                            <td>{{ $integration->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No integrations found for this vendor.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
