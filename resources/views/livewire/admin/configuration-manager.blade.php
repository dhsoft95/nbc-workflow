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
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'categories' ? 'active bg-white text-blue' : 'text-white' }}"
                       href="#"
                       wire:click.prevent="changeTab('categories')">
                        <i class="fa fa-folder"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'items' ? 'active bg-white text-blue' : 'text-white' }}"
                       href="#"
                       wire:click.prevent="changeTab('items')">
                        <i class="fa fa-list"></i> Configuration Items
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'history' ? 'active bg-white text-blue' : 'text-white' }}"
                       href="#"
                       wire:click.prevent="changeTab('history')">
                        <i class="fa fa-history"></i> History
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <!-- Search Box -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-search"></i></span>
                </div>
                <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="Search...">
                @if($activeTab === 'categories')
                    <div class="input-group-append">
                        <button class="btn btn-blue" wire:click="openCategoryModal" style="background-color: #152755; color: white;">
                            <i class="fa fa-plus"></i> Add Category
                        </button>
                    </div>
                @elseif($activeTab === 'items')
                    <div class="input-group-append">
                        <button class="btn btn-blue" wire:click="openItemModal" style="background-color: #152755; color: white;">
                            <i class="fa fa-plus"></i> Add Item
                        </button>
                    </div>
                @endif
            </div>

            <!-- Category Filter for Items Tab -->
            @if($activeTab === 'items')
                <div class="form-group">
                    <label for="categoryFilter">Filter by Category:</label>
                    <select wire:model="selectedCategoryId" wire:change="filterItemsByCategory" id="categoryFilter" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- History Filters -->
            @if($activeTab === 'history')
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Filter History</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Category:</label>
                                    <select wire:model="historyFilters.category_id" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Item:</label>
                                    <select wire:model="historyFilters.item_id" class="form-control">
                                        <option value="">All Items</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Action:</label>
                                    <select wire:model="historyFilters.action" class="form-control">
                                        <option value="">All Actions</option>
                                        <option value="category_created">Category Created</option>
                                        <option value="category_updated">Category Updated</option>
                                        <option value="category_deleted">Category Deleted</option>
                                        <option value="item_created">Item Created</option>
                                        <option value="item_updated">Item Updated</option>
                                        <option value="item_deleted">Item Deleted</option>
                                        <option value="item_activated">Item Activated</option>
                                        <option value="item_deactivated">Item Deactivated</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>From Date:</label>
                                    <input type="date" wire:model="historyFilters.date_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>To Date:</label>
                                    <input type="date" wire:model="historyFilters.date_to" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-group mb-0 w-100">
                                    <button wire:click="applyHistoryFilters" class="btn btn-blue mr-2" style="background-color: #152755; color: white;">
                                        <i class="fa fa-filter"></i> Apply Filters
                                    </button>
                                    <button wire:click="resetHistoryFilters" class="btn btn-secondary">
                                        <i class="fa fa-refresh"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Categories Table -->
            @if($activeTab === 'categories')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Key</th>
                            <th>Description</th>
                            <th width="180px">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td><code>{{ $category->key }}</code></td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    <button wire:click="openEditCategoryModal({{ $category->id }})" class="btn btn-sm btn-blue" style="background-color: #152755; color: white;">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button wire:click="openDeleteCategoryModal({{ $category->id }})" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No categories found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $categories->links() }}
                </div>
            @endif

            <!-- Items Table -->
            @if($activeTab === 'items')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                        <tr>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th width="180px">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->category->name }}</td>
                                <td>{{ $item->name }}</td>
                                <td><code>{{ Str::limit($item->value, 30) }}</code></td>
                                <td>{{ Str::limit($item->description, 30) }}</td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status-{{ $item->id }}"
                                               {{ $item->is_active ? 'checked' : '' }}
                                               wire:click="toggleItemStatus({{ $item->id }})">
                                        <label class="custom-control-label" for="status-{{ $item->id }}">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </td>
                                <td>{{ $item->display_order }}</td>
                                <td>
                                    <button wire:click="openEditItemModal({{ $item->id }})" class="btn btn-sm btn-blue" style="background-color: #152755; color: white;">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button wire:click="openDeleteItemModal({{ $item->id }})" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No configuration items found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $items->links() }}
                </div>
            @endif

            <!-- History Table -->
            @if($activeTab === 'history')
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Item</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($history as $record)
                            <tr>
                                <td>{{ $record->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>{{ $record->user ? $record->user->name : 'System' }}</td>
                                <td>{{ $record->category ? $record->category->name : 'N/A' }}</td>
                                <td>{{ $record->item ? $record->item->name : 'N/A' }}</td>
                                <td>
                                        <span class="badge badge-{{
                                            str_contains($record->action, 'created') ? 'success' :
                                            (str_contains($record->action, 'updated') || str_contains($record->action, 'activated') ? 'info' :
                                            (str_contains($record->action, 'deleted') || str_contains($record->action, 'deactivated') ? 'danger' : 'secondary'))
                                        }}">
                                            {{ ucwords(str_replace('_', ' ', $record->action)) }}
                                        </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" data-toggle="popover" data-trigger="focus" title="Change Details"
                                            data-content="Old: {{ $record->old_value ? substr($record->old_value, 0, 100) . '...' : 'N/A' }}
                                                            New: {{ $record->new_value ? substr($record->new_value, 0, 100) . '...' : 'N/A' }}">
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No history records found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $history->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Category Modal -->
    @if($isOpenCategoryModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #152755; color: white;">
                        <h5 class="modal-title">{{ $categoryId ? 'Edit Category' : 'Create New Category' }}</h5>
                        <button type="button" class="close text-white" wire:click="closeCategoryModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveCategory">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" wire:model="categoryName" wire:keyup="generateKey" class="form-control" placeholder="Enter category name">
                                @error('categoryName') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Key</label>
                                <input type="text" wire:model="categoryKey" class="form-control" placeholder="Enter category key">
                                <small class="form-text text-muted">Only letters, numbers, and underscores are allowed</small>
                                @error('categoryKey') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea wire:model="categoryDescription" class="form-control" rows="3" placeholder="Enter category description"></textarea>
                                @error('categoryDescription') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeCategoryModal" class="btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-blue" style="background-color: #152755; color: white;">
                                {{ $categoryId ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Category Delete Confirmation Modal -->
    @if($isDeleteCategoryModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="close text-white" wire:click="closeCategoryModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this category? This action cannot be undone.</p>
                        <p class="text-danger">Note: Categories with associated items cannot be deleted. You must delete all items in this category first.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeCategoryModal" class="btn btn-secondary">Cancel</button>
                        <button type="button" wire:click="deleteCategory" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Item Modal -->
    @if($isOpenItemModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #152755; color: white;">
                        <h5 class="modal-title">{{ $itemId ? 'Edit Configuration Item' : 'Create New Configuration Item' }}</h5>
                        <button type="button" class="close text-white" wire:click="closeItemModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveItem">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select wire:model="selectedCategoryId" class="form-control">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedCategoryId') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" wire:model="itemName" class="form-control" placeholder="Enter item name">
                                        @error('itemName') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Value</label>
                                <textarea wire:model="itemValue" class="form-control" rows="3" placeholder="Enter item value"></textarea>
                                @error('itemValue') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label>Description</label>
                                <textarea wire:model="itemDescription" class="form-control" rows="2" placeholder="Enter item description"></textarea>
                                @error('itemDescription') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Active Status</label>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="itemIsActive"
                                                   wire:model="itemIsActive">
                                            <label class="custom-control-label" for="itemIsActive">
                                                {{ $itemIsActive ? 'Active' : 'Inactive' }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Display Order</label>
                                        <input type="number" wire:model="itemDisplayOrder" class="form-control" min="0">
                                        @error('itemDisplayOrder') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeItemModal" class="btn btn-secondary">Close</button>
                            <button type="submit" class="btn btn-blue" style="background-color: #152755; color: white;">
                                {{ $itemId ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Item Delete Confirmation Modal -->
    @if($isDeleteItemModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="close text-white" wire:click="closeItemModal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this configuration item? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeItemModal" class="btn btn-secondary">Cancel</button>
                        <button type="button" wire:click="deleteItem" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Initialize popovers for history details -->
    <script>
        document.addEventListener('livewire:load', function () {
            $(function () {
                $('[data-toggle="popover"]').popover({
                    html: true
                });
            });
        });

        // Re-initialize popovers after Livewire updates the DOM
        window.addEventListener('livewire:update', function () {
            $(function () {
                $('[data-toggle="popover"]').popover({
                    html: true
                });
            });
        });
    </script>
</div>
