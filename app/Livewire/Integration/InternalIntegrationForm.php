<?php

namespace App\Livewire\Integration;

use App\Models\ConfigurationCategory;
use App\Models\Integration;
use App\Models\InternalIntegration;
use Illuminate\Support\Facades\Log;

class InternalIntegrationForm extends BaseIntegrationForm
{
    // Internal integration fields
    public $middleware_connection = '';
    public $cms_binding = false;
    public $cms_binding_details = '';
    public $api_specifications = '';
    public $security_classification = '';
    public $responsible_team = '';
    public $features_supported = [];
    public $system_dependencies = '';

    // Dropdowns data
    public $middlewareOptions = [];
    public $securityClassifications = [];
    public $responsibleTeams = [];
    public $featuresOptions = [];

    protected function loadFormData()
    {
        // Load dropdown data
        $this->loadDropdownOptions();

        // Convert boolean values to ensure proper handling
        $this->cms_binding = (bool)$this->cms_binding;
    }

    protected function loadDropdownOptions()
    {
        $categories = [
            'middleware_connections' => 'middlewareOptions',
            'security_classifications' => 'securityClassifications',
            'responsible_teams' => 'responsibleTeams',
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

            Log::info('Internal integration dropdown options loaded successfully');
        } catch (\Exception $e) {
            Log::error('Error loading internal integration dropdown options: ' . $e->getMessage());
            $this->dispatch('error', message: 'Failed to load form options');
        }
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
                // Internal integration details validation
                return [
                    'middleware_connection' => 'nullable|string',
                    'cms_binding' => 'boolean',
                    'cms_binding_details' => 'required_if:cms_binding,1,true',
                    'api_specifications' => 'nullable|string',
                    'security_classification' => 'required|string',
                    'responsible_team' => 'required|string',
                    'features_supported' => 'nullable|array',
                    'system_dependencies' => 'nullable|string',
                ];

            case 3:
                // Review - no additional validation
                return [];

            default:
                return [];
        }
    }

    protected function saveTypeSpecificData(Integration $integration)
    {
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

        Log::info('Internal integration details created for integration ID: ' . $integration->id);
    }

    protected function getTypeSpecificValidationRules()
    {
        return [
            'middleware_connection' => 'nullable|string',
            'cms_binding' => 'boolean',
            'cms_binding_details' => 'required_if:cms_binding,1,true',
            'api_specifications' => 'nullable|string',
            'security_classification' => 'required|string',
            'responsible_team' => 'required|string',
            'features_supported' => 'nullable|array',
            'system_dependencies' => 'nullable|string',
        ];
    }

    protected function resetTypeSpecificFields()
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

    protected function getIntegrationType()
    {
        return 'internal';
    }

    public function render()
    {
        return view('livewire.integration.internal-form')
            ->layout('layouts.app', ['title' => 'Create Internal Integration']);
    }
}
