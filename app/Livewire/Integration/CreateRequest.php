<?php

namespace App\Livewire\Integration;

use App\Models\Integration;
use App\Models\InternalIntegration;
use App\Models\ExternalIntegration;
use App\Models\Vendor;
use App\Models\ConfigurationCategory;
use App\Models\ConfigurationItem;
use App\Models\ApprovalHistory;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateRequest extends Component
{
    use WithFileUploads;

    // Form step tracking
    public $currentStep = 1;
    public $totalSteps = 4;

    // Form completion status for steps
    public $stepCompleted = [
        1 => false, // General info
        2 => false, // Integration specific details
        3 => false, // File uploads
        4 => false  // Review
    ];

    // Common form fields
    public $type = 'internal';
    public $name = '';
    public $purpose = '';
    public $department = '';
    public $priority = 'medium';
    public $priority_justification = '';
    public $resource_requirements = '';
    public $estimated_timeline = '';

    // Internal integration fields
    public $middleware_connection = '';
    public $cms_binding = false;
    public $cms_binding_details = '';
    public $api_specifications = '';
    public $security_classification = '';
    public $responsible_team = '';
    public $features_supported = [];
    public $system_dependencies = ''; // Changed from array to string

    // External integration fields
    public $is_new_vendor = true;
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
    public $middlewareOptions = [];
    public $securityClassifications = [];
    public $responsibleTeams = [];
    public $connectionMethods = [];
    public $authMethods = [];
    public $dataFormats = [];
    public $featuresOptions = [];
    public $vendors = [];

    // Form session storage
    protected $draft_key = 'integration_draft';
    public $has_draft = false;

    // Listeners
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'saveProgress' => 'saveFormProgress'
    ];

    // Properties to skip from validation to prevent errors
    protected $exceptFromValidation = ['api_documentation_file', 'contract_file', 'test_plan_file'];

    public function mount()
    {
        Log::info('CreateRequest component mounted');

        // Set default department from user if available
        $this->department = Auth::user()->department ?? '';

        // Ensure boolean fields are properly typed
        $this->cms_binding = (bool)$this->cms_binding;
        $this->is_new_vendor = (bool)$this->is_new_vendor;
        $this->legal_approval = (bool)$this->legal_approval;
        $this->compliance_approval = (bool)$this->compliance_approval;

        // Load dropdown options and vendors
        $this->loadDropdownOptions();
        $this->vendors = Vendor::orderBy('name')->get();

        // Check for existing draft
        $this->checkForDraft();
    }

    public function checkForDraft()
    {
        $draft = session($this->draft_key);
        if ($draft) {
            $this->has_draft = true;
        }
    }

    public function loadDraft()
    {
        Log::info('Loading draft from session');

        $draft = session($this->draft_key);
        if ($draft) {
            foreach ($draft as $key => $value) {
                if (property_exists($this, $key)) {
                    // Handle boolean fields correctly
                    if (in_array($key, ['cms_binding', 'is_new_vendor', 'legal_approval', 'compliance_approval'])) {
                        $this->$key = (bool)$value;
                    } else {
                        $this->$key = $value;
                    }
                }
            }
            session()->flash('message', 'Draft loaded successfully!');
        }
    }

    public function discardDraft()
    {
        Log::info('Discarding draft');
        session()->forget($this->draft_key);
        $this->has_draft = false;
        $this->resetForm();
        session()->flash('message', 'Draft discarded successfully!');
    }

    public function saveFormProgress()
    {
        Log::info('Saving form progress to session');

        // Convert object to array and exclude file upload properties
        $properties = get_object_vars($this);
        $data = array_diff_key($properties, array_flip([
            'api_documentation_file', 'contract_file', 'test_plan_file'
        ]));

        session([$this->draft_key => $data]);
        $this->has_draft = true;

        session()->flash('message', 'Progress saved automatically!');
    }

    public function goToStep($step)
    {
        Log::info('Attempting to go to step: ' . $step);

        // Validate current step before proceeding
        if ($step > $this->currentStep) {
            if (!$this->validateCurrentStep()) {
                Log::warning('Cannot proceed to step ' . $step . ' - validation failed');
                return;
            }
        }

        // If trying to go to step 3 (files) from step 1, must go through step 2 first
        if ($this->currentStep == 1 && $step == 3) {
            $this->currentStep = 2;
            return;
        }

        $this->currentStep = $step;

        Log::info('Successfully moved to step: ' . $step);
    }

    public function nextStep()
    {
        Log::info('Next step button clicked. Current step: ' . $this->currentStep);

        if ($this->validateCurrentStep()) {
            $this->stepCompleted[$this->currentStep] = true;

            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;

                // Skip file upload step if internal integration
                if ($this->currentStep == 3 && $this->type == 'internal') {
                    $this->currentStep++;
                }

                Log::info('Moving to next step: ' . $this->currentStep);
            }

            // Save progress when moving to next step
            $this->saveFormProgress();
        } else {
            Log::warning('Cannot move to next step - validation failed');
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;

            // Skip file upload step if internal integration when going backwards
            if ($this->currentStep == 3 && $this->type == 'internal') {
                $this->currentStep--;
            }

            Log::info('Moving to previous step: ' . $this->currentStep);
        }
    }

    protected function validateCurrentStep()
    {
        $validationRules = $this->getStepValidationRules($this->currentStep);
        Log::info('Validating step ' . $this->currentStep, ['rules' => $validationRules]);

        try {
            $validated = $this->validate($validationRules);
            Log::info('Step ' . $this->currentStep . ' validation successful');
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Step ' . $this->currentStep . ' validation failed', ['errors' => $e->errors()]);
            $this->emit('errorBag', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error during validation: ' . $e->getMessage());
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            return false;
        }
    }

    protected function getStepValidationRules($step)
    {
        $rules = [];

        switch ($step) {
            case 1:
                // General info validation
                $rules = [
                    'name' => 'required|string|max:255',
                    'purpose' => 'required|string',
                    'department' => 'required|string|max:255',
                    'priority' => 'required|in:low,medium,high',
                    'priority_justification' => 'required_if:priority,high',
                    'resource_requirements' => 'nullable|string',
                    'estimated_timeline' => 'nullable|date',
                    'type' => 'required|in:internal,external',
                ];
                break;

            case 2:
                // Type-specific validation
                if ($this->type === 'internal') {
                    $rules = [
                        'middleware_connection' => 'nullable|string',
                        'cms_binding' => 'boolean',
                        'cms_binding_details' => 'required_if:cms_binding,1,true',
                        'api_specifications' => 'nullable|string',
                        'security_classification' => 'required|string',
                        'responsible_team' => 'required|string',
                        'features_supported' => 'nullable|array',
                        'system_dependencies' => 'nullable|string',
                    ];
                } else {
                    $rules = [
                        'is_new_vendor' => 'boolean',
                        'vendor_id' => 'required_if:is_new_vendor,0,false|nullable|exists:vendors,id',
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
                }
                break;

            case 3:
                // File uploads validation - more permissive to avoid blocking progress
                $rules = [
                    'api_documentation_file' => 'nullable|file|max:10240',
                    'contract_file' => 'nullable|file|max:10240',
                    'test_plan_file' => 'nullable|file|max:10240',
                ];
                break;

            case 4:
                // Review step - no additional validation
                break;
        }

        return $rules;
    }

    public function loadDropdownOptions()
    {
        $categories = [
            'middleware_connections' => 'middlewareOptions',
            'security_classifications' => 'securityClassifications',
            'responsible_teams' => 'responsibleTeams',
            'connection_methods' => 'connectionMethods',
            'authentication_methods' => 'authMethods',
            'data_formats' => 'dataFormats',
            'supported_features' => 'featuresOptions',
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
                            'value' => $item->value ?? $item->id, // Use value field if available, otherwise ID
                            'label' => $item->name
                        ];
                    })->toArray();
                }
            }

            Log::info('Dropdown options loaded successfully');
        } catch (\Exception $e) {
            Log::error('Error loading dropdown options: ' . $e->getMessage());
            $this->dispatchBrowserEvent('error', ['message' => 'Failed to load form options']);
        }
    }

    public function updatedVendorId()
    {
        $this->is_new_vendor = empty($this->vendor_id);
        Log::info('Vendor ID updated', ['vendor_id' => $this->vendor_id, 'is_new_vendor' => $this->is_new_vendor]);
    }

    public function updatedIsNewVendor($value)
    {
        // Convert string value from radio buttons to boolean
        $this->is_new_vendor = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($this->is_new_vendor) {
            $this->vendor_id = null;
        }

        Log::info('Is new vendor updated', ['is_new_vendor' => $this->is_new_vendor]);
    }

    public function updatedType($value)
    {
        Log::info('Integration type changed to: ' . $value);

        // Reset the irrelevant fields when type changes
        if ($value === 'internal') {
            $this->resetExternalFields();
        } else {
            $this->resetInternalFields();
        }

        // Reset step 2 completion since fields changed
        $this->stepCompleted[2] = false;
    }

    protected function resetInternalFields()
    {
        $this->middleware_connection = '';
        $this->cms_binding = false;
        $this->cms_binding_details = '';
        $this->api_specifications = '';
        $this->security_classification = '';
        $this->responsible_team = '';
        $this->features_supported = [];
        $this->system_dependencies = '';

        Log::info('Internal integration fields reset');
    }

    protected function resetExternalFields()
    {
        $this->is_new_vendor = true;
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

        // Reset file uploads
        $this->api_documentation_file = null;
        $this->contract_file = null;
        $this->test_plan_file = null;

        Log::info('External integration fields reset');
    }

    public function resetForm()
    {
        Log::info('Resetting form to defaults');

        // Reset all form fields
        $this->reset([
            'name', 'purpose', 'department', 'priority', 'priority_justification',
            'resource_requirements', 'estimated_timeline'
        ]);

        // Reset to defaults
        $this->type = 'internal';
        $this->priority = 'medium';
        $this->currentStep = 1;

        // Reset file uploads
        $this->api_documentation_file = null;
        $this->contract_file = null;
        $this->test_plan_file = null;

        // Reset type-specific fields
        $this->resetInternalFields();
        $this->resetExternalFields();

        // Reset step completion
        foreach (array_keys($this->stepCompleted) as $step) {
            $this->stepCompleted[$step] = false;
        }
    }

    public function save()
    {
        Log::info('Save method called (form submission)');

        // Add a small artificial delay to prevent double-submissions
        // This is a workaround for potential race conditions with button clicks
        usleep(300000); // 300ms

        try {
            // Make sure we're on the final step
            if ($this->currentStep != $this->totalSteps) {
                $this->currentStep = $this->totalSteps;
            }

            // Use a transaction for database operations
            DB::beginTransaction();

            // Validate all required fields
            $this->validate($this->getFinalValidationRules());

            Log::info('Final validation passed, creating integration');

            // Create the base integration
            $integration = Integration::create([
                'name' => $this->name,
                'purpose' => $this->purpose,
                'department' => $this->department,
                'type' => $this->type,
                'status' => 'submitted',
                'priority' => $this->priority,
                'priority_justification' => $this->priority_justification,
                'resource_requirements' => $this->resource_requirements,
                'estimated_timeline' => $this->estimated_timeline,
                'created_by' => Auth::id(),
            ]);

            // Create the specific integration type (internal or external)
            if ($this->type === 'internal') {
                InternalIntegration::create([
                    'integration_id' => $integration->id,
                    'middleware_connection' => $this->middleware_connection,
                    'cms_binding' => (bool)$this->cms_binding,
                    'cms_binding_details' => $this->cms_binding_details,
                    'api_specifications' => $this->api_specifications,
                    'security_classification' => $this->security_classification,
                    'responsible_team' => $this->responsible_team,
                    'features_supported' => $this->features_supported,
                    'system_dependencies' => $this->system_dependencies,
                ]);

                Log::info('Internal integration details created');
            } else {
                ExternalIntegration::create([
                    'integration_id' => $integration->id,
                    'is_new_vendor' => (bool)$this->is_new_vendor,
                    'vendor_id' => $this->vendor_id,
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

                Log::info('External integration details created');
            }

            // Handle file uploads - wrapped in try/catch to handle potential file upload issues
            if ($this->type === 'external') {
                try {
                    $this->processFileUploads($integration);
                } catch (\Exception $e) {
                    Log::error('File upload error: ' . $e->getMessage());
                    // Continue without failing if file uploads have issues
                }
            }

            // Record initial approval history
            ApprovalHistory::create([
                'integration_id' => $integration->id,
                'stage' => 'request',
                'action' => 'submitted',
                'user_id' => Auth::id(),
                'comments' => 'Integration request submitted and pending App Owner approval',
            ]);

            DB::commit();
            Log::info('Integration request created successfully with ID: ' . $integration->id);

            // Clear the draft after successful submission
            session()->forget($this->draft_key);
            $this->has_draft = false;

            session()->flash('message', 'Integration request created successfully!');

            // Emit event for JS
            $this->dispatchBrowserEvent('form-submitted', ['integration_id' => $integration->id]);

            // Redirect to the integration details page
            return redirect()->route('integrations.show', $integration);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Validation failed during submission', ['errors' => $e->errors()]);

            // Let Livewire handle this automatically, but ensure we stay on the current step
            $this->dispatchBrowserEvent('form-validation-failed', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating integration request: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error creating integration request: ' . $e->getMessage());
            $this->dispatchBrowserEvent('form-submission-error', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function processFileUploads($integration)
    {
        $files = [
            'api_documentation_file' => 'api_documentation',
            'contract_file' => 'contract',
            'test_plan_file' => 'test_plan',
        ];

        foreach ($files as $fileField => $fileType) {
            if ($this->{$fileField} && is_object($this->{$fileField})) {
                try {
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

                    Log::info("File uploaded: {$fileType}", [
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $path
                    ]);
                } catch (\Exception $e) {
                    Log::error("File upload failed for {$fileField}: " . $e->getMessage());
                    throw new \Exception("Failed to upload {$fileType} file: " . $e->getMessage());
                }
            }
        }
    }

    private function getFinalValidationRules()
    {
        $commonRules = [
            'name' => 'required|string|max:255',
            'purpose' => 'required|string',
            'department' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'priority_justification' => 'required_if:priority,high',
            'resource_requirements' => 'nullable|string',
            'estimated_timeline' => 'nullable|date',
            'type' => 'required|in:internal,external',
        ];

        $typeSpecificRules = $this->type === 'internal' ? [
            'middleware_connection' => 'nullable|string',
            'cms_binding' => 'boolean',
            'cms_binding_details' => 'required_if:cms_binding,1,true',
            'api_specifications' => 'nullable|string',
            'security_classification' => 'required|string',
            'responsible_team' => 'required|string',
            'features_supported' => 'nullable|array',
            'system_dependencies' => 'nullable|string',
        ] : [
            'is_new_vendor' => 'boolean',
            'vendor_id' => 'required_if:is_new_vendor,0,false|nullable|exists:vendors,id',
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

        $fileRules = [];
        if ($this->type === 'external') {
            $fileRules = [
                'api_documentation_file' => 'nullable|file|max:10240',
                'contract_file' => 'nullable|file|max:10240',
                'test_plan_file' => 'nullable|file|max:10240',
            ];
        }

        return array_merge($commonRules, $typeSpecificRules, $fileRules);
    }

    public function render()
    {
        return view('livewire.integration.create-request')
            ->layout('layouts.app', ['title' => 'Create Integration Request']);
    }
}
