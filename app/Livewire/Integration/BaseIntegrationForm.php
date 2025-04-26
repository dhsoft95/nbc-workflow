<?php

namespace App\Livewire\Integration;

use App\Models\Integration;
use App\Models\ApprovalHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

abstract class BaseIntegrationForm extends Component
{
    // Form step tracking
    public $currentStep = 1;
    public $totalSteps = 3; // Default, child classes can override

    // Form completion status for steps
    public $stepCompleted = [
        1 => false, // General info
        2 => false, // Integration specific details
        3 => false  // Review
    ];

    // Common form fields
    public $name = '';
    public $purpose = '';
    public $department = '';
    public $priority = 'medium';
    public $priority_justification = '';
    public $resource_requirements = '';
    public $estimated_timeline = '';

    // Form session storage
    protected $draft_key;
    public $has_draft = false;

    // Listeners for Livewire v3
    protected function getListeners()
    {
        return [
            'refreshComponent' => '$refresh',
            'saveProgress' => 'saveFormProgress'
        ];
    }

    public function mount()
    {
        Log::info('Integration form component mounted: ' . get_class($this));

        // Set default department from user if available
        $this->department = Auth::user()->department ?? '';

        // Set draft key based on class name
        $this->draft_key = 'integration_draft_' . strtolower(class_basename($this));

        // Load additional data needed by the form
        $this->loadFormData();

        // Check for existing draft
        $this->checkForDraft();
    }

    // Abstract methods to be implemented by child classes
    abstract protected function loadFormData();
    abstract protected function getStepValidationRules($step);
    abstract protected function saveTypeSpecificData(Integration $integration);
    abstract protected function getTypeSpecificValidationRules();
    abstract protected function resetTypeSpecificFields();
    abstract protected function getIntegrationType();

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
                    $this->$key = $value;
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

        // Convert object to array and exclude any file upload properties
        $properties = get_object_vars($this);
        $filePropNames = $this->getFilePropertyNames();
        $data = array_diff_key($properties, array_flip($filePropNames));

        session([$this->draft_key => $data]);
        $this->has_draft = true;

        session()->flash('message', 'Progress saved automatically!');
    }

    // Child classes should override this if they have file uploads
    protected function getFilePropertyNames()
    {
        return [];
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
            Log::info('Moving to previous step: ' . $this->currentStep);
        }
    }

    protected function validateCurrentStep()
    {
        $validationRules = $this->getStepValidationRules($this->currentStep);
        Log::info('Validating step ' . $this->currentStep, ['rules' => $validationRules]);

        try {
            // Use Laravel's Validator facade directly
            $validator = Validator::make(
                $this->all(),
                $validationRules
            );

            if ($validator->fails()) {
                Log::warning('Step ' . $this->currentStep . ' validation failed', ['errors' => $validator->errors()->toArray()]);

                // Add errors to the error bag
                foreach ($validator->errors()->toArray() as $key => $messages) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }
                return false;
            }

            Log::info('Step ' . $this->currentStep . ' validation successful');
            return true;
        } catch (\Exception $e) {
            Log::error('Unexpected error during validation: ' . $e->getMessage());
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
            return false;
        }
    }

    // Helper method to get all the properties for validation
    public function all()
    {
        $properties = get_object_vars($this);
        // Filter out non-data properties that shouldn't be validated
        $excluded = [
            'id', 'middlewareOptions', 'securityClassifications',
            'responsibleTeams', 'featuresOptions', 'stepCompleted',
            'currentStep', 'totalSteps', 'draft_key', 'has_draft'
        ];

        return array_diff_key($properties, array_flip($excluded));
    }

    public function resetForm()
    {
        Log::info('Resetting form to defaults');

        // Reset common form fields
        $this->reset([
            'name', 'purpose', 'department', 'priority_justification',
            'resource_requirements', 'estimated_timeline'
        ]);

        // Reset to defaults
        $this->priority = 'medium';
        $this->currentStep = 1;

        // Reset step completion
        foreach (array_keys($this->stepCompleted) as $step) {
            $this->stepCompleted[$step] = false;
        }

        // Let child classes reset their specific fields
        $this->resetTypeSpecificFields();
    }

    public function save()
    {
        Log::info('Save method called (form submission)');

        // Add a small artificial delay to prevent double-submissions
        usleep(300000); // 300ms

        try {
            // Make sure we're on the final step
            if ($this->currentStep != $this->totalSteps) {
                $this->currentStep = $this->totalSteps;
            }

            // Use a transaction for database operations
            DB::beginTransaction();

            // Get all validation rules
            $validationRules = $this->getFinalValidationRules();
            Log::info('Final validation rules:', ['rules' => $validationRules]);

            // Use Laravel's Validator facade directly
            $validator = Validator::make(
                $this->all(),
                $validationRules
            );

            if ($validator->fails()) {
                Log::warning('Validation failed during submission', ['errors' => $validator->errors()->toArray()]);

                // Add errors to the error bag
                foreach ($validator->errors()->toArray() as $key => $messages) {
                    foreach ($messages as $message) {
                        $this->addError($key, $message);
                    }
                }

                DB::rollBack();
                return;
            }

            Log::info('Final validation passed, creating integration');

            // Create the base integration
            $integration = Integration::create([
                'name' => $this->name,
                'purpose' => $this->purpose,
                'department' => $this->department,
                'type' => $this->getIntegrationType(),
                'status' => 'submitted', // Initial status is 'submitted'
                'priority' => $this->priority,
                'priority_justification' => $this->priority_justification,
                'resource_requirements' => $this->resource_requirements,
                'estimated_timeline' => $this->estimated_timeline,
                'created_by' => Auth::id(),
            ]);

            // Let child class save its specific data
            $this->saveTypeSpecificData($integration);

            // Record initial approval history
            // According to the requirements, this is the "Request" stage in the approval workflow
            // Using 'approved' action since the enum doesn't have 'submitted' as a valid value
            ApprovalHistory::create([
                'integration_id' => $integration->id,
                'stage' => 'request', // First stage in the workflow
                'action' => 'approved', // Using 'approved' since the enum doesn't have 'submitted'
                'user_id' => Auth::id(),
                'comments' => 'Integration request submitted and pending App Owner approval',
            ]);

            DB::commit();
            Log::info('Integration request created successfully with ID: ' . $integration->id);

            // Clear the draft after successful submission
            session()->forget($this->draft_key);
            $this->has_draft = false;

            session()->flash('message', 'Integration request created successfully!');

            // Notify relevant stakeholders (App Owners) that a new request needs their approval
            $this->dispatch('form-submitted', integration_id: $integration->id);

            // Redirect to the integration details page
            return redirect()->route('integrations.show', $integration);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating integration request: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Error creating integration request: ' . $e->getMessage());
            $this->dispatch('form-submission-error', message: $e->getMessage());
            return null;
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
        ];

        return array_merge($commonRules, $this->getTypeSpecificValidationRules());
    }
}
