<?php

namespace App\Livewire\Admin;

use App\Models\Vendor;
use Livewire\Component;
use Livewire\WithPagination;

class VendorManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form state variables
    public $vendor_id;
    public $name;
    public $contact_email;
    public $contact_phone;
    public $address;
    public $website;
    public $description;

    // UI state variables
    public $isOpen = false;
    public $isDeleteModalOpen = false;
    public $isViewModalOpen = false;
    public $search = '';
    public $currentVendor;
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'contact_email' => 'required|email|max:255',
        'contact_phone' => 'nullable|max:20',
        'address' => 'nullable|max:500',
        'website' => 'nullable|url|max:255',
        'description' => 'nullable|max:1000',
    ];

    public function render()
    {
        $searchTerm = '%' . $this->search . '%';

        $vendors = Vendor::where('name', 'like', $searchTerm)
            ->orWhere('contact_email', 'like', $searchTerm)
            ->orWhere('contact_phone', 'like', $searchTerm)
            ->orWhere('website', 'like', $searchTerm)
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.vendor-management', [
            'vendors' => $vendors
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function openEditModal($id)
    {
        $vendor = Vendor::findOrFail($id);
        $this->vendor_id = $id;
        $this->name = $vendor->name;
        $this->contact_email = $vendor->contact_email;
        $this->contact_phone = $vendor->contact_phone;
        $this->address = $vendor->address;
        $this->website = $vendor->website;
        $this->description = $vendor->description;
        $this->resetErrorBag();
        $this->isOpen = true;
    }

    public function openDeleteModal($id)
    {
        $this->vendor_id = $id;
        $this->isDeleteModalOpen = true;
    }

    public function openViewModal($id)
    {
        $this->currentVendor = Vendor::with('externalIntegrations')->findOrFail($id);
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
        $this->vendor_id = null;
        $this->name = '';
        $this->contact_email = '';
        $this->contact_phone = '';
        $this->address = '';
        $this->website = '';
        $this->description = '';
        $this->resetErrorBag();
    }

    public function store()
    {
        $this->validate();

        $vendorData = [
            'name' => $this->name,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'address' => $this->address,
            'website' => $this->website,
            'description' => $this->description,
        ];

        if ($this->vendor_id) {
            $vendor = Vendor::findOrFail($this->vendor_id);
            $vendor->update($vendorData);
            session()->flash('message', 'Vendor updated successfully.');
        } else {
            Vendor::create($vendorData);
            session()->flash('message', 'Vendor created successfully.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete()
    {
        if ($this->vendor_id) {
            $vendor = Vendor::findOrFail($this->vendor_id);

            // Check if the vendor has associations
            if ($vendor->externalIntegrations()->count() > 0) {
                session()->flash('error', 'Cannot delete vendor with associated integrations.');
                $this->closeModal();
                return;
            }

            $vendor->delete();
            session()->flash('message', 'Vendor deleted successfully.');
        }

        $this->closeModal();
    }

    public function restore($id)
    {
        Vendor::withTrashed()->findOrFail($id)->restore();
        session()->flash('message', 'Vendor restored successfully.');
    }

    public function forceDelete($id)
    {
        $vendor = Vendor::withTrashed()->findOrFail($id);

        // Check if the vendor has associations
        if ($vendor->externalIntegrations()->count() > 0) {
            session()->flash('error', 'Cannot permanently delete vendor with associated integrations.');
            return;
        }

        $vendor->forceDelete();
        session()->flash('message', 'Vendor permanently deleted.');
    }
}
