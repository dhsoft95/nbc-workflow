<?php

namespace App\Livewire\Admin;

use App\Models\ConfigurationCategory;
use App\Models\ConfigurationItem;
use App\Models\ConfigurationHistory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ConfigurationManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Common variables
    public $activeTab = 'categories';
    public $search = '';

    // Category variables
    public $categoryId;
    public $categoryName;
    public $categoryKey;
    public $categoryDescription;
    public $isOpenCategoryModal = false;
    public $isDeleteCategoryModal = false;

    // Item variables
    public $itemId;
    public $selectedCategoryId;
    public $itemName;
    public $itemValue;
    public $itemDescription;
    public $itemIsActive = true;
    public $itemDisplayOrder = 0;
    public $isOpenItemModal = false;
    public $isDeleteItemModal = false;

    // History variables
    public $historyFilters = [
        'category_id' => '',
        'item_id' => '',
        'action' => '',
        'date_from' => '',
        'date_to' => '',
    ];

    // Listeners for events
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Initialize component
    }

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        // Query data based on active tab
        if ($this->activeTab === 'categories') {
            $categories = ConfigurationCategory::where('name', 'like', $searchTerm)
                ->orWhere('key', 'like', $searchTerm)
                ->orWhere('description', 'like', $searchTerm)
                ->orderBy('name')
                ->paginate(10);

            return view('livewire.admin.configuration-manager', [
                'categories' => $categories,
                'items' => collect(),
                'history' => collect(),
            ]);
        }
        elseif ($this->activeTab === 'items') {
            $query = ConfigurationItem::with('category');

            if ($this->selectedCategoryId) {
                $query->where('category_id', $this->selectedCategoryId);
            }

            $items = $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('value', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            })
                ->orderBy('display_order')
                ->paginate(10);

            $categories = ConfigurationCategory::orderBy('name')->get();

            return view('livewire.admin.configuration-manager', [
                'categories' => $categories,
                'items' => $items,
                'history' => collect(),
            ]);
        }
        else { // history tab
            $query = ConfigurationHistory::with(['category', 'item', 'user']);

            if ($this->historyFilters['category_id']) {
                $query->where('category_id', $this->historyFilters['category_id']);
            }

            if ($this->historyFilters['item_id']) {
                $query->where('item_id', $this->historyFilters['item_id']);
            }

            if ($this->historyFilters['action']) {
                $query->where('action', $this->historyFilters['action']);
            }

            if ($this->historyFilters['date_from']) {
                $query->whereDate('created_at', '>=', $this->historyFilters['date_from']);
            }

            if ($this->historyFilters['date_to']) {
                $query->whereDate('created_at', '<=', $this->historyFilters['date_to']);
            }

            $history = $query->orderBy('created_at', 'desc')
                ->paginate(10);

            $categories = ConfigurationCategory::orderBy('name')->get();
            $items = ConfigurationItem::orderBy('name')->get();

            return view('livewire.admin.configuration-manager', [
                'categories' => $categories,
                'items' => $items,
                'history' => $history,
            ]);
        }
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Category Methods
    public function openCategoryModal()
    {
        $this->resetCategoryFields();
        $this->isOpenCategoryModal = true;
    }

    public function openEditCategoryModal($id)
    {
        $category = ConfigurationCategory::findOrFail($id);
        $this->categoryId = $id;
        $this->categoryName = $category->name;
        $this->categoryKey = $category->key;
        $this->categoryDescription = $category->description;
        $this->isOpenCategoryModal = true;
    }

    public function openDeleteCategoryModal($id)
    {
        $this->categoryId = $id;
        $this->isDeleteCategoryModal = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'categoryName' => 'required|min:3|max:255',
            'categoryKey' => 'required|min:2|max:100|regex:/^[a-z0-9_]+$/i',
            'categoryDescription' => 'nullable|max:1000',
        ], [
            'categoryKey.regex' => 'The key field can only contain letters, numbers, and underscores.',
        ]);

        if ($this->categoryId) {
            $category = ConfigurationCategory::findOrFail($this->categoryId);
            $oldValues = [
                'name' => $category->name,
                'key' => $category->key,
                'description' => $category->description,
            ];

            $category->update([
                'name' => $this->categoryName,
                'key' => $this->categoryKey,
                'description' => $this->categoryDescription,
            ]);

            // Log history for update
            ConfigurationHistory::create([
                'category_id' => $category->id,
                'item_id' => null,
                'action' => 'category_updated',
                'old_value' => json_encode($oldValues),
                'new_value' => json_encode([
                    'name' => $category->name,
                    'key' => $category->key,
                    'description' => $category->description,
                ]),
                'user_id' => auth()->id(),
            ]);

            session()->flash('message', 'Category updated successfully.');
        } else {
            $category = ConfigurationCategory::create([
                'name' => $this->categoryName,
                'key' => $this->categoryKey,
                'description' => $this->categoryDescription,
            ]);

            // Log history for creation
            ConfigurationHistory::create([
                'category_id' => $category->id,
                'item_id' => null,
                'action' => 'category_created',
                'old_value' => null,
                'new_value' => json_encode([
                    'name' => $category->name,
                    'key' => $category->key,
                    'description' => $category->description,
                ]),
                'user_id' => auth()->id(),
            ]);

            session()->flash('message', 'Category created successfully.');
        }

        $this->closeCategoryModal();
    }

    public function deleteCategory()
    {
        $category = ConfigurationCategory::findOrFail($this->categoryId);

        // Check if category has items
        if ($category->items()->count() > 0) {
            session()->flash('error', 'Cannot delete category with associated items. Please delete the items first.');
            $this->closeCategoryModal();
            return;
        }

        // Log history before deletion
        ConfigurationHistory::create([
            'category_id' => null,
            'item_id' => null,
            'action' => 'category_deleted',
            'old_value' => json_encode([
                'id' => $category->id,
                'name' => $category->name,
                'key' => $category->key,
                'description' => $category->description,
            ]),
            'new_value' => null,
            'user_id' => auth()->id(),
        ]);

        $category->delete();
        session()->flash('message', 'Category deleted successfully.');
        $this->closeCategoryModal();
    }

    public function closeCategoryModal()
    {
        $this->isOpenCategoryModal = false;
        $this->isDeleteCategoryModal = false;
        $this->resetCategoryFields();
    }

    private function resetCategoryFields()
    {
        $this->categoryId = null;
        $this->categoryName = '';
        $this->categoryKey = '';
        $this->categoryDescription = '';
        $this->resetErrorBag();
    }

    // Item Methods
    public function generateKey()
    {
        if ($this->categoryName) {
            $this->categoryKey = Str::slug($this->categoryName, '_');
        }
    }

    public function openItemModal()
    {
        $this->resetItemFields();
        $this->isOpenItemModal = true;
    }

    public function openEditItemModal($id)
    {
        $item = ConfigurationItem::findOrFail($id);
        $this->itemId = $id;
        $this->selectedCategoryId = $item->category_id;
        $this->itemName = $item->name;
        $this->itemValue = $item->value;
        $this->itemDescription = $item->description;
        $this->itemIsActive = $item->is_active;
        $this->itemDisplayOrder = $item->display_order;
        $this->isOpenItemModal = true;
    }

    public function openDeleteItemModal($id)
    {
        $this->itemId = $id;
        $this->isDeleteItemModal = true;
    }

    public function saveItem()
    {
        $this->validate([
            'selectedCategoryId' => 'required|exists:configuration_categories,id',
            'itemName' => 'required|min:3|max:255',
            'itemValue' => 'required',
            'itemDescription' => 'nullable|max:1000',
            'itemDisplayOrder' => 'required|integer|min:0',
        ]);

        if ($this->itemId) {
            $item = ConfigurationItem::findOrFail($this->itemId);
            $oldValues = [
                'category_id' => $item->category_id,
                'name' => $item->name,
                'value' => $item->value,
                'description' => $item->description,
                'is_active' => $item->is_active,
                'display_order' => $item->display_order,
            ];

            $item->update([
                'category_id' => $this->selectedCategoryId,
                'name' => $this->itemName,
                'value' => $this->itemValue,
                'description' => $this->itemDescription,
                'is_active' => $this->itemIsActive,
                'display_order' => $this->itemDisplayOrder,
            ]);

            // Log history for update
            ConfigurationHistory::create([
                'category_id' => $item->category_id,
                'item_id' => $item->id,
                'action' => 'item_updated',
                'old_value' => json_encode($oldValues),
                'new_value' => json_encode([
                    'category_id' => $item->category_id,
                    'name' => $item->name,
                    'value' => $item->value,
                    'description' => $item->description,
                    'is_active' => $item->is_active,
                    'display_order' => $item->display_order,
                ]),
                'user_id' => auth()->id(),
            ]);

            session()->flash('message', 'Configuration item updated successfully.');
        } else {
            $item = ConfigurationItem::create([
                'category_id' => $this->selectedCategoryId,
                'name' => $this->itemName,
                'value' => $this->itemValue,
                'description' => $this->itemDescription,
                'is_active' => $this->itemIsActive,
                'display_order' => $this->itemDisplayOrder,
            ]);

            // Log history for creation
            ConfigurationHistory::create([
                'category_id' => $item->category_id,
                'item_id' => $item->id,
                'action' => 'item_created',
                'old_value' => null,
                'new_value' => json_encode([
                    'category_id' => $item->category_id,
                    'name' => $item->name,
                    'value' => $item->value,
                    'description' => $item->description,
                    'is_active' => $item->is_active,
                    'display_order' => $item->display_order,
                ]),
                'user_id' => auth()->id(),
            ]);

            session()->flash('message', 'Configuration item created successfully.');
        }

        $this->closeItemModal();
    }

    public function deleteItem()
    {
        $item = ConfigurationItem::findOrFail($this->itemId);

        // Log history before deletion
        ConfigurationHistory::create([
            'category_id' => $item->category_id,
            'item_id' => null,
            'action' => 'item_deleted',
            'old_value' => json_encode([
                'id' => $item->id,
                'category_id' => $item->category_id,
                'name' => $item->name,
                'value' => $item->value,
                'description' => $item->description,
                'is_active' => $item->is_active,
                'display_order' => $item->display_order,
            ]),
            'new_value' => null,
            'user_id' => auth()->id(),
        ]);

        $item->delete();
        session()->flash('message', 'Configuration item deleted successfully.');
        $this->closeItemModal();
    }

    public function closeItemModal()
    {
        $this->isOpenItemModal = false;
        $this->isDeleteItemModal = false;
        $this->resetItemFields();
    }

    private function resetItemFields()
    {
        $this->itemId = null;
        $this->itemName = '';
        $this->itemValue = '';
        $this->itemDescription = '';
        $this->itemIsActive = true;
        $this->itemDisplayOrder = 0;
        $this->resetErrorBag();
    }

    // Filter methods for history
    public function applyHistoryFilters()
    {
        $this->resetPage();
    }

    public function resetHistoryFilters()
    {
        $this->historyFilters = [
            'category_id' => '',
            'item_id' => '',
            'action' => '',
            'date_from' => '',
            'date_to' => '',
        ];
        $this->resetPage();
    }

    public function toggleItemStatus($id)
    {
        $item = ConfigurationItem::findOrFail($id);
        $oldValue = $item->is_active;
        $item->is_active = !$oldValue;
        $item->save();

        // Log history for status change
        ConfigurationHistory::create([
            'category_id' => $item->category_id,
            'item_id' => $item->id,
            'action' => $oldValue ? 'item_deactivated' : 'item_activated',
            'old_value' => json_encode(['is_active' => $oldValue]),
            'new_value' => json_encode(['is_active' => $item->is_active]),
            'user_id' => auth()->id(),
        ]);

        session()->flash('message', 'Item status updated successfully.');
    }

    public function filterItemsByCategory()
    {
        $this->resetPage();
    }
}
