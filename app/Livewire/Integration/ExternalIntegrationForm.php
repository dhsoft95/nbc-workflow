<?php

namespace App\Livewire\Integration;

use App\Models\Integration;
use App\Models\ExternalIntegration;
use App\Models\Vendor;
use App\Models\Attachment;
use App\Models\ConfigurationCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;

class ExternalIntegrationForm extends BaseIntegrationForm
{
    use WithFileUploads;

    // Override total steps
    public $totalSteps = 4;

    // Update step completion array
    public function mount()
    {
        // Step completion for 4 steps
        $this->stepCompleted = [
            1 => false, // General info
            2 => false, // External integration details
            3 => false, // Attachments
            4 => false  // Review
        ];

        // Set default vendor selection
        $this->vendor_selection = 'new';

        // Call parent mount
        parent::mount();
    }

    // External integration fields
    public $vendor_selection = 'new'; // 'new' or 'existing'
    public $vendor_id = null;
    public $connection_method = '';
    public $network_requirements = '';
    public $authentication_method = '';
    public $data_encryption_requirements = '';
    public $api_documentation_url = '';
    public $rate_limiting = '';
    public $data_formats = [];
    public $contract_expiration = '';
    public $sla_terms = '';
    public $legal_approval = false;
    public $compliance_approval = false;
    public $sit_outcome = null;
    public $test_plan = '';
    public $issue_log = '';
    public $business_impact = '';

    // File uploads
    public $api_documentation_file;
    public $contract_file;
    public $test_plan_file;

    // Dropdowns data
    public $vendors = [];
    public $connectionMethods = [];
    public $authMethods = [];
    public $dataFormats = [];

    protected function loadFormData()
    {
        // Load vendors and dropdown data
        $this->vendors = Vendor::orderBy('name')->get();
        $this->loadDropdownOptions();

        // Convert boolean values to ensure proper handling
        $this->legal_approval = (bool)$this->legal_approval;
        $this->compliance_approval = (bool)$this->compliance_approval;
    }

    protected function getFilePropertyNames()
    {
        return ['api_documentation_file', 'contract_file', 'test_plan_file'];
    }

    protected function loadDropdownOptions()
    {
        $categories = [
            'connection_methods' => 'connectionMethods',
            'authentication_methods' => 'authMethods',
            'data_formats' => 'dataFormats',
        ];

        try {
            foreach ($categories as $key => $property) {
                $category = ConfigurationCategory::where('key', $key)->first();
                if ($category) {
                    $items = $category->items()
                        ->where('is_active', true)
                        ->orderBy('display_order')
                        ->get();

                    $this->{$property} = $items->map(function ($item) {
                        return [
                            'value' => $item->value ?? $item->id,
                            'label' => $item->name
                        ];
                    })->toArray();
                }
            }

            Log::info('External integration dropdown options loaded successfully');
        } catch (\Exception $e) {
            Log::error('Error loading external integration dropdown options: ' . $e->getMessage());
            $this->dispatchBrowserEvent('error', ['message' => 'Failed to load form options']);
        }
    }

    // Method for handling vendor dropdown selection changes
    public function updatedVendorId($value)
    {
        // If a vendor is selected, ensure we're in 'existing' mode
        if (!empty($value)) {
            $this->vendor_selection = 'existing';
        }

        // Log for debugging
        Log::info('Vendor ID changed', [
            'vendor_id' => $value,
            'selection' => $this->vendor_selection
        ]);
    }

    // Method for handling vendor selection type changes (new/existing)
    public function updatedVendorSelection($value)
    {
        // Clear vendor_id if switching to new vendor
        if ($value === 'new') {
            $this->vendor_id = null;
        }

        // Log for debugging
        Log::info('Vendor selection changed', [
            'selection' => $value,
            'vendor_id' => $this->vendor_id
        ]);
    }

    protected function getStepValidationRules($step)
    {
        switch ($step) {
            case 1:
                // General info validation
                return [
                    'name' => 'required|string|max:255',
                    'purpose' => 'required|string',
                    'department' => 'required|string|max:255',
                    'priority' => 'required|in:low,medium,high',
                    'priority_justification' => 'required_if:priority,high',
                    'resource_requirements' => 'nullable|string',
                    'estimated_timeline' => 'nullable|date',
                ];

            case 2:
                // External integration details validation with updated vendor validation
                return [
                    'vendor_selection' => 'required|in:new,existing',
                    'vendor_id' => 'required_if:vendor_selection,existing|nullable|exists:vendors,id',
                    'connection_method' => 'required|string',
                    'network_requirements' => 'nullable|string',
                    'authentication_method' => 'required|string',
                    'data_encryption_requirements' => 'nullable|string',
                    'api_documentation_url' => 'nullable|url',
                    'rate_limiting' => 'nullable|string',
                    'data_formats' => 'required|array|min:1',
                    'contract_expiration' => 'nullable|date',
                    'sla_terms' => 'nullable|string',
                    'legal_approval' => 'boolean',
                    'compliance_approval' => 'boolean',
                    'sit_outcome' => 'nullable|in:wip,successful,failed',
                    'test_plan' => 'nullable|string',
                    'issue_log' => 'nullable|string',
                    'business_impact' => 'required|string',
                ];

            case 3:
                // File uploads validation - more permissive to avoid blocking progress
                return [
                    'api_documentation_file' => 'nullable|file|max:10240',
                    'contract_file' => 'nullable|file|max:10240',
                    'test_plan_file' => 'nullable|file|max:10240',
                ];

            case 4:
                // Review - no additional validation
                return [];

            default:
                return [];
        }
    }

    protected function saveTypeSpecificData(Integration $integration)
    {
        // Create external integration record
        ExternalIntegration::create([
            'integration_id' => $integration->id,
            'is_new_vendor' => $this->vendor_selection === 'new',
            'vendor_id' => $this->vendor_selection === 'existing' ? $this->vendor_id : null,
            'connection_method' => $this->connection_method,
            'network_requirements' => $this->network_requirements,
            'authentication_method' => $this->authentication_method,
            'data_encryption_requirements' => $this->data_encryption_requirements,
            'api_documentation_url' => $this->api_documentation_url,
            'rate_limiting' => $this->rate_limiting,
            'data_formats' => $this->data_formats,
            'contract_expiration' => $this->contract_expiration,
            'sla_terms' => $this->sla_terms,
            'legal_approval' => (bool)$this->legal_approval,
            'compliance_approval' => (bool)$this->compliance_approval,
            'sit_outcome' => $this->sit_outcome,
            'test_plan' => $this->test_plan,
            'issue_log' => $this->issue_log,
            'business_impact' => $this->business_impact,
        ]);

        Log::info('External integration details created for integration ID: ' . $integration->id);

        // Process file uploads
        $this->processFileUploads($integration);
    }

    private function processFileUploads($integration)
    {
        $files = [
            'api_documentation_file' => 'api_documentation',
            'contract_file' => 'contract',
            'test_plan_file' => 'test_plan',
        ];

        foreach ($files as $fileField => $fileType) {
            if ($this->{$fileField}) {
                try {
                    // Check if it's a valid file object
                    if (is_object($this->{$fileField}) && method_exists($this->{$fileField}, 'store')) {
                        $file = $this->{$fileField};
                        $path = $file->store('attachments/' . $integration->id, 'public');

                        Attachment::create([
                            'integration_id' => $integration->id,
                            'type' => $fileType,
                            'filename' => basename($path),
                            'original_filename' => $file->getClientOriginalName(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                            'path' => $path,
                            'uploaded_by' => Auth::id(),
                        ]);

                        // Clear the file property after successful upload
                        $this->{$fileField} = null;

                        Log::info("File uploaded: {$fileType}", [
                            'original_name' => $file->getClientOriginalName(),
                            'path' => $path
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("File upload failed for {$fileField}: " . $e->getMessage());
                    // Continue without failing if file uploads have issues
                }
            }
        }
    }

    protected function getTypeSpecificValidationRules()
    {
        $rules = [
            'vendor_selection' => 'required|in:new,existing',
            'vendor_id' => 'required_if:vendor_selection,existing|nullable|exists:vendors,id',
            'connection_method' => 'required|string',
            'network_requirements' => 'nullable|string',
            'authentication_method' => 'required|string',
            'data_encryption_requirements' => 'nullable|string',
            'api_documentation_url' => 'nullable|url',
            'rate_limiting' => 'nullable|string',
            'data_formats' => 'required|array|min:1',
            'contract_expiration' => 'nullable|date',
            'sla_terms' => 'nullable|string',
            'legal_approval' => 'boolean',
            'compliance_approval' => 'boolean',
            'sit_outcome' => 'nullable|in:wip,successful,failed',
            'test_plan' => 'nullable|string',
            'issue_log' => 'nullable|string',
            'business_impact' => 'required|string',

            // File validation is optional
            'api_documentation_file' => 'nullable|file|max:10240',
            'contract_file' => 'nullable|file|max:10240',
            'test_plan_file' => 'nullable|file|max:10240',
        ];

        return $rules;
    }

    protected function resetTypeSpecificFields()
    {
        $this->vendor_selection = 'new';
        $this->vendor_id = null;
        $this->connection_method = '';
        $this->network_requirements = '';
        $this->authentication_method = '';
        $this->data_encryption_requirements = '';
        $this->api_documentation_url = '';
        $this->rate_limiting = '';
        $this->data_formats = [];
        $this->contract_expiration = '';
        $this->sla_terms = '';
        $this->legal_approval = false;
        $this->compliance_approval = false;
        $this->sit_outcome = null;
        $this->test_plan = '';
        $this->issue_log = '';
        $this->business_impact = '';

        // Reset file uploads - important to prevent serialization errors
        $this->api_documentation_file = null;
        $this->contract_file = null;
        $this->test_plan_file = null;

        Log::info('External integration fields reset');
    }

    protected function getIntegrationType()
    {
        return 'external';
    }

    public function dehydrate()
    {
        // Reset file properties when serializing to prevent errors except on steps 3 and 4
        if (!in_array($this->currentStep, [3, 4])) {
            $this->api_documentation_file = $this->api_documentation_file ? true : null;
            $this->contract_file = $this->contract_file ? true : null;
            $this->test_plan_file = $this->test_plan_file ? true : null;
        }
    }

    public function render()
    {
        return view('livewire.integration.external-form')
            ->layout('layouts.app', ['title' => 'Create External Integration']);
    }
}
