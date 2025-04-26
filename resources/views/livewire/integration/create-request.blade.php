@extends('layouts.app')

@section('content')
    @extends('layouts.app')

    @section('content')
        <div>
            <!-- Debug panel (only for development) -->
            @if(config('app.env') !== 'production')
                <div id="livewire-debug" class="mb-3 p-2 border bg-light" style="display: none;">
                    <h6>Debug Info</h6>
                    <div>Current Step: <span id="current-step">{{ $currentStep }}</span></div>
                    <div>Step Completed: <span id="step-completed">{{ json_encode($stepCompleted) }}</span></div>
                    <div>Type: <span id="type-value">{{ $type }}</span></div>
                    <div>Form Valid: <span id="form-valid">Unknown</span></div>
                    <div id="debug-messages"></div>
                    <button class="btn btn-sm btn-info mt-2" onclick="document.getElementById('livewire-debug').style.display = 'none'">Hide</button>
                </div>
                <button class="btn btn-sm btn-outline-secondary mb-3" onclick="document.getElementById('livewire-debug').style.display = 'block'">Show Debug</button>
            @endif

            <!-- Draft management section -->
            @if($has_draft)
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        <strong>You have a saved draft!</strong>
                        Would you like to continue where you left off?
                    </div>
                    <div>
                        <button wire:click="loadDraft" class="btn btn-primary btn-sm mr-2">Load Draft</button>
                        <button wire:click="discardDraft" class="btn btn-danger btn-sm">Discard</button>
                    </div>
                </div>
            @endif

            <!-- Flash messages -->
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Progress bar -->
            <div class="mb-4">
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                         role="progressbar"
                         style="width: {{ ($currentStep / $totalSteps) * 100 }}%">
                        Step {{ $currentStep }} of {{ $totalSteps }}
                    </div>
                </div>
            </div>

            <!-- Steps navigation -->
            <div class="step-navigation mb-4">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-link {{ $currentStep == 1 ? 'disabled' : '' }}"
                            wire:click="goToStep(1)" {{ $currentStep == 1 ? 'disabled' : '' }}>
                        <span class="badge {{ $stepCompleted[1] ? 'badge-success' : 'badge-secondary' }}">1</span> General Info
                    </button>
                    <button type="button" class="btn btn-link {{ !$stepCompleted[1] ? 'disabled' : '' }}"
                            wire:click="goToStep(2)" {{ !$stepCompleted[1] ? 'disabled' : '' }}>
                        <span class="badge {{ $stepCompleted[2] ? 'badge-success' : 'badge-secondary' }}">2</span> Integration Details
                    </button>
                    @if($type == 'external')
                        <button type="button" class="btn btn-link {{ !$stepCompleted[2] ? 'disabled' : '' }}"
                                wire:click="goToStep(3)" {{ !$stepCompleted[2] ? 'disabled' : '' }}>
                            <span class="badge {{ $stepCompleted[3] ? 'badge-success' : 'badge-secondary' }}">3</span> Attachments
                        </button>
                    @endif
                    <button type="button" class="btn btn-link {{ !$stepCompleted[2] && ($type == 'external' && !$stepCompleted[3]) ? 'disabled' : '' }}"
                            wire:click="goToStep(4)" {{ !$stepCompleted[2] && ($type == 'external' && !$stepCompleted[3]) ? 'disabled' : '' }}>
                        <span class="badge {{ $stepCompleted[4] ? 'badge-success' : 'badge-secondary' }}">{{ $type == 'external' ? '4' : '3' }}</span> Review
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="save">
                <!-- Step 1: General Information -->
                <div class="card mb-4 {{ $currentStep != 1 ? 'd-none' : '' }}">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Step 1: General Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="type">Integration Type</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="type-internal" name="type" class="custom-control-input" value="internal"
                                       wire:model.defer="type">
                                <label class="custom-control-label" for="type-internal">Internal Integration</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="type-external" name="type" class="custom-control-input" value="external"
                                       wire:model.defer="type">
                                <label class="custom-control-label" for="type-external">External Integration</label>
                            </div>
                            @error('type') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Integration Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                   wire:model.defer="name" placeholder="Enter integration name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="purpose">Purpose</label>
                            <textarea class="form-control @error('purpose') is-invalid @enderror" id="purpose"
                                      wire:model.defer="purpose" rows="3" placeholder="Describe the purpose of this integration"></textarea>
                            @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" id="department"
                                   wire:model.defer="department" placeholder="Enter your department">
                            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select class="form-control @error('priority') is-invalid @enderror" id="priority" wire:model="priority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group {{ $priority != 'high' ? 'd-none' : '' }}">
                            <label for="priority_justification">Priority Justification</label>
                            <textarea class="form-control @error('priority_justification') is-invalid @enderror" id="priority_justification"
                                      wire:model.defer="priority_justification" rows="2" placeholder="Justify the high priority"></textarea>
                            @error('priority_justification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="resource_requirements">Resource Requirements</label>
                            <textarea class="form-control @error('resource_requirements') is-invalid @enderror" id="resource_requirements"
                                      wire:model.defer="resource_requirements" rows="2" placeholder="Describe resource requirements"></textarea>
                            @error('resource_requirements') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="estimated_timeline">Estimated Completion Date</label>
                            <input type="date" class="form-control @error('estimated_timeline') is-invalid @enderror" id="estimated_timeline"
                                   wire:model.defer="estimated_timeline">
                            @error('estimated_timeline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Step 2: Integration Details -->
                <div class="card mb-4 {{ $currentStep != 2 ? 'd-none' : '' }}">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Step 2: {{ $type == 'internal' ? 'Internal' : 'External' }} Integration Details</h5>
                    </div>
                    <div class="card-body">
                        @if($type == 'internal')
                            <!-- Internal Integration Form Fields -->
                            <div class="form-group">
                                <label for="middleware_connection">Middleware Connection</label>
                                <select class="form-control @error('middleware_connection') is-invalid @enderror" id="middleware_connection"
                                        wire:model.defer="middleware_connection">
                                    <option value="">Select a middleware connection</option>
                                    @foreach($middlewareOptions as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('middleware_connection') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="cms_binding"
                                           wire:model="cms_binding">
                                    <label class="custom-control-label" for="cms_binding">CMS Binding Required</label>
                                </div>
                                @error('cms_binding') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group {{ !$cms_binding ? 'd-none' : '' }}">
                                <label for="cms_binding_details">CMS Binding Details</label>
                                <textarea class="form-control @error('cms_binding_details') is-invalid @enderror" id="cms_binding_details"
                                          wire:model.defer="cms_binding_details" rows="2" placeholder="Provide details about the CMS binding"></textarea>
                                @error('cms_binding_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="api_specifications">API Specifications</label>
                                <textarea class="form-control @error('api_specifications') is-invalid @enderror" id="api_specifications"
                                          wire:model.defer="api_specifications" rows="3" placeholder="Enter API specifications"></textarea>
                                @error('api_specifications') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="security_classification">Security Classification</label>
                                <select class="form-control @error('security_classification') is-invalid @enderror" id="security_classification"
                                        wire:model.defer="security_classification">
                                    <option value="">Select security classification</option>
                                    @foreach($securityClassifications as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('security_classification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="responsible_team">Responsible Team</label>
                                <select class="form-control @error('responsible_team') is-invalid @enderror" id="responsible_team"
                                        wire:model.defer="responsible_team">
                                    <option value="">Select responsible team</option>
                                    @foreach($responsibleTeams as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('responsible_team') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label>Features Supported</label>
                                <div class="row">
                                    @foreach($featuresOptions as $option)
                                        <div class="col-md-6">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="feature-{{ $option['value'] }}"
                                                       wire:model.defer="features_supported" value="{{ $option['value'] }}">
                                                <label class="custom-control-label" for="feature-{{ $option['value'] }}">{{ $option['label'] }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('features_supported') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="system_dependencies">System Dependencies</label>
                                <textarea class="form-control @error('system_dependencies') is-invalid @enderror" id="system_dependencies"
                                          wire:model.defer="system_dependencies" rows="2" placeholder="List system dependencies"></textarea>
                                @error('system_dependencies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @else
                            <!-- External Integration Form Fields -->
                            <div class="form-group">
                                <label>Vendor</label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="is-new-vendor-yes" name="is_new_vendor" class="custom-control-input" value="1"
                                           wire:model="is_new_vendor">
                                    <label class="custom-control-label" for="is-new-vendor-yes">New Vendor</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="is-new-vendor-no" name="is_new_vendor" class="custom-control-input" value="0"
                                           wire:model="is_new_vendor">
                                    <label class="custom-control-label" for="is-new-vendor-no">Existing Vendor</label>
                                </div>
                                @error('is_new_vendor') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group {{ $is_new_vendor ? 'd-none' : '' }}">
                                <label for="vendor_id">Select Vendor</label>
                                <select class="form-control @error('vendor_id') is-invalid @enderror" id="vendor_id"
                                        wire:model.defer="vendor_id">
                                    <option value="">Select vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                                @error('vendor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="connection_method">Connection Method</label>
                                <select class="form-control @error('connection_method') is-invalid @enderror" id="connection_method"
                                        wire:model.defer="connection_method">
                                    <option value="">Select connection method</option>
                                    @foreach($connectionMethods as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('connection_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="network_requirements">Network Requirements</label>
                                <textarea class="form-control @error('network_requirements') is-invalid @enderror" id="network_requirements"
                                          wire:model.defer="network_requirements" rows="2" placeholder="Describe network requirements"></textarea>
                                @error('network_requirements') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="authentication_method">Authentication Method</label>
                                <select class="form-control @error('authentication_method') is-invalid @enderror" id="authentication_method"
                                        wire:model.defer="authentication_method">
                                    <option value="">Select authentication method</option>
                                    @foreach($authMethods as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('authentication_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="data_encryption_requirements">Data Encryption Requirements</label>
                                <textarea class="form-control @error('data_encryption_requirements') is-invalid @enderror" id="data_encryption_requirements"
                                          wire:model.defer="data_encryption_requirements" rows="2" placeholder="Describe encryption requirements"></textarea>
                                @error('data_encryption_requirements') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="api_documentation_url">API Documentation URL</label>
                                <input type="url" class="form-control @error('api_documentation_url') is-invalid @enderror" id="api_documentation_url"
                                       wire:model.defer="api_documentation_url" placeholder="Enter API documentation URL">
                                @error('api_documentation_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="rate_limiting">Rate Limiting</label>
                                <input type="text" class="form-control @error('rate_limiting') is-invalid @enderror" id="rate_limiting"
                                       wire:model.defer="rate_limiting" placeholder="Enter rate limiting details">
                                @error('rate_limiting') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label>Data Formats</label>
                                <div class="row">
                                    @foreach($dataFormats as $option)
                                        <div class="col-md-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="format-{{ $option['value'] }}"
                                                       wire:model.defer="data_formats" value="{{ $option['value'] }}">
                                                <label class="custom-control-label" for="format-{{ $option['value'] }}">{{ $option['label'] }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('data_formats') <div class="text-danger">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="contract_expiration">Contract Expiration Date</label>
                                <input type="date" class="form-control @error('contract_expiration') is-invalid @enderror" id="contract_expiration"
                                       wire:model.defer="contract_expiration">
                                @error('contract_expiration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="sla_terms">SLA Terms</label>
                                <textarea class="form-control @error('sla_terms') is-invalid @enderror" id="sla_terms"
                                          wire:model.defer="sla_terms" rows="2" placeholder="Enter SLA terms"></textarea>
                                @error('sla_terms') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="legal_approval"
                                                   wire:model.defer="legal_approval">
                                            <label class="custom-control-label" for="legal_approval">Legal Approval Obtained</label>
                                        </div>
                                        @error('legal_approval') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="compliance_approval"
                                                   wire:model.defer="compliance_approval">
                                            <label class="custom-control-label" for="compliance_approval">Compliance Approval Obtained</label>
                                        </div>
                                        @error('compliance_approval') <div class="text-danger">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sit_outcome">SIT Outcome</label>
                                <select class="form-control @error('sit_outcome') is-invalid @enderror" id="sit_outcome"
                                        wire:model.defer="sit_outcome">
                                    <option value="">Select SIT outcome</option>
                                    <option value="wip">Work in Progress</option>
                                    <option value="successful">Successful</option>
                                    <option value="failed">Failed</option>
                                </select>
                                @error('sit_outcome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="test_plan">Test Plan</label>
                                <textarea class="form-control @error('test_plan') is-invalid @enderror" id="test_plan"
                                          wire:model.defer="test_plan" rows="2" placeholder="Enter test plan details"></textarea>
                                @error('test_plan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="issue_log">Issue Log</label>
                                <textarea class="form-control @error('issue_log') is-invalid @enderror" id="issue_log"
                                          wire:model.defer="issue_log" rows="2" placeholder="Enter any known issues"></textarea>
                                @error('issue_log') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="business_impact">Business Impact</label>
                                <textarea class="form-control @error('business_impact') is-invalid @enderror" id="business_impact"
                                          wire:model.defer="business_impact" rows="3" placeholder="Describe the business impact"></textarea>
                                @error('business_impact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Step 3: File Uploads (Only for external integrations) -->
                <div class="card mb-4 {{ $currentStep != 3 || $type != 'external' ? 'd-none' : '' }}">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Step 3: File Attachments</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="api_documentation_file">API Documentation</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('api_documentation_file') is-invalid @enderror"
                                       id="api_documentation_file" wire:model="api_documentation_file">
                                <label class="custom-file-label" for="api_documentation_file">
                                    {{ $api_documentation_file ? (is_object($api_documentation_file) ? pathinfo($api_documentation_file->getClientOriginalName(), PATHINFO_BASENAME) : $api_documentation_file) : 'Choose file' }}
                                </label>
                                @error('api_documentation_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <small class="form-text text-muted">Upload API documentation if available (max 10MB)</small>
                        </div>

                        <div class="form-group">
                            <label for="contract_file">Contract Document</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('contract_file') is-invalid @enderror"
                                       id="contract_file" wire:model="contract_file">
                                <label class="custom-file-label" for="contract_file">
                                    {{ $contract_file ? (is_object($contract_file) ? pathinfo($contract_file->getClientOriginalName(), PATHINFO_BASENAME) : $contract_file) : 'Choose file' }}
                                </label>
                                @error('contract_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <small class="form-text text-muted">Upload contract document if available (max 10MB)</small>
                        </div>

                        <div class="form-group">
                            <label for="test_plan_file">Test Plan Document</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('test_plan_file') is-invalid @enderror"
                                       id="test_plan_file" wire:model="test_plan_file">
                                <label class="custom-file-label" for="test_plan_file">
                                    {{ $test_plan_file ? (is_object($test_plan_file) ? pathinfo($test_plan_file->getClientOriginalName(), PATHINFO_BASENAME) : $test_plan_file) : 'Choose file' }}
                                </label>
                                @error('test_plan_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <small class="form-text text-muted">Upload test plan document if available (max 10MB)</small>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Review -->
                <div class="card mb-4 {{ $currentStep != 4 ? 'd-none' : '' }}">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Step 4: Review & Submit</h5>
                    </div>
                    <div class="card-body">
                        <h5>General Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">Integration Type</th>
                                <td>{{ ucfirst($type) }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $name }}</td>
                            </tr>
                            <tr>
                                <th>Purpose</th>
                                <td>{{ $purpose }}</td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td>{{ $department }}</td>
                            </tr>
                            <tr>
                                <th>Priority</th>
                                <td>
                                    {{ ucfirst($priority) }}
                                    @if($priority == 'high')
                                        <br><small class="text-muted">Justification: {{ $priority_justification }}</small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Resource Requirements</th>
                                <td>{{ $resource_requirements }}</td>
                            </tr>
                            <tr>
                                <th>Estimated Timeline</th>
                                <td>{{ $estimated_timeline ? date('F j, Y', strtotime($estimated_timeline)) : 'Not specified' }}</td>
                            </tr>
                        </table>

                        @if($type == 'internal')
                            <!-- Internal Integration Review -->
                            <h5 class="mt-4">Internal Integration Details</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Middleware Connection</th>
                                    <td>{{ $middleware_connection }}</td>
                                </tr>
                                <tr>
                                    <th>CMS Binding</th>
                                    <td>
                                        {{ $cms_binding ? 'Yes' : 'No' }}
                                        @if($cms_binding)
                                            <br><small class="text-muted">Details: {{ $cms_binding_details }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>API Specifications</th>
                                    <td>{{ $api_specifications }}</td>
                                </tr>
                                <tr>
                                    <th>Security Classification</th>
                                    <td>{{ $security_classification }}</td>
                                </tr>
                                <tr>
                                    <th>Responsible Team</th>
                                    <td>{{ $responsible_team }}</td>
                                </tr>
                                <tr>
                                    <th>Features Supported</th>
                                    <td>
                                        @if(count($features_supported) > 0)
                                            <ul class="mb-0 pl-3">
                                                @foreach($features_supported as $feature)
                                                    <li>{{ $feature }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            None specified
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>System Dependencies</th>
                                    <td>{{ is_array($system_dependencies) ? implode(', ', $system_dependencies) : $system_dependencies }}</td>
                                </tr>
                            </table>
                        @else
                            <!-- External Integration Review -->
                            <h5 class="mt-4">External Integration Details</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Vendor</th>
                                    <td>
                                        @if($is_new_vendor)
                                            New Vendor
                                        @else
                                            Existing Vendor: {{ optional($vendors->where('id', $vendor_id)->first())->name ?? 'Not selected' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Connection Method</th>
                                    <td>{{ $connection_method }}</td>
                                </tr>
                                <tr>
                                    <th>Network Requirements</th>
                                    <td>{{ $network_requirements }}</td>
                                </tr>
                                <tr>
                                    <th>Authentication Method</th>
                                    <td>{{ $authentication_method }}</td>
                                </tr>
                                <tr>
                                    <th>Data Encryption Requirements</th>
                                    <td>{{ $data_encryption_requirements }}</td>
                                </tr>
                                <tr>
                                    <th>API Documentation URL</th>
                                    <td>{{ $api_documentation_url }}</td>
                                </tr>
                                <tr>
                                    <th>Rate Limiting</th>
                                    <td>{{ $rate_limiting }}</td>
                                </tr>
                                <tr>
                                    <th>Data Formats</th>
                                    <td>
                                        @if(is_array($data_formats) && count($data_formats) > 0)
                                            <ul class="mb-0 pl-3">
                                                @foreach($data_formats as $format)
                                                    <li>{{ $format }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            None specified
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Contract Expiration</th>
                                    <td>{{ $contract_expiration ? date('F j, Y', strtotime($contract_expiration)) : 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>SLA Terms</th>
                                    <td>{{ $sla_terms }}</td>
                                </tr>
                                <tr>
                                    <th>Approvals</th>
                                    <td>
                                        Legal: {{ $legal_approval ? 'Yes' : 'No' }}<br>
                                        Compliance: {{ $compliance_approval ? 'Yes' : 'No' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>SIT Outcome</th>
                                    <td>
                                        @if($sit_outcome == 'wip')
                                            Work in Progress
                                        @elseif($sit_outcome == 'successful')
                                            Successful
                                        @elseif($sit_outcome == 'failed')
                                            Failed
                                        @else
                                            Not specified
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Test Plan</th>
                                    <td>{{ $test_plan }}</td>
                                </tr>
                                <tr>
                                    <th>Issue Log</th>
                                    <td>{{ $issue_log }}</td>
                                </tr>
                                <tr>
                                    <th>Business Impact</th>
                                    <td>{{ $business_impact }}</td>
                                </tr>
                            </table>

                            <!-- Files Review -->
                            <h5 class="mt-4">Uploaded Files</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">API Documentation</th>
                                    <td>{{ is_object($api_documentation_file) ? $api_documentation_file->getClientOriginalName() : 'Not uploaded' }}</td>
                                </tr>
                                <tr>
                                    <th>Contract Document</th>
                                    <td>{{ is_object($contract_file) ? $contract_file->getClientOriginalName() : 'Not uploaded' }}</td>
                                </tr>
                                <tr>
                                    <th>Test Plan Document</th>
                                    <td>{{ is_object($test_plan_file) ? $test_plan_file->getClientOriginalName() : 'Not uploaded' }}</td>
                                </tr>
                            </table>
                        @endif

                        <!-- Terms and confirmation -->
                        <div class="alert alert-info mt-4">
                            <p class="mb-0">
                                <i class="fas fa-info-circle mr-2"></i>
                                By submitting this integration request, you confirm that all the information provided is accurate to the best of your knowledge.
                                The request will be submitted for approval by the relevant stakeholders.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form navigation and submit buttons -->
                <div class="d-flex justify-content-between mb-5">
                    <button type="button" class="btn btn-secondary" wire:click="previousStep" {{ $currentStep == 1 ? 'disabled' : '' }}>
                        <i class="fas fa-chevron-left mr-1"></i> Previous
                    </button>

                    <div>
                        @if($currentStep < $totalSteps)
                            <button type="button" class="btn btn-primary" id="next-step-button" wire:click="nextStep">
                                Next <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        @else
                            <button type="submit" class="btn btn-success" id="submit-button">
                                <i class="fas fa-check-circle mr-1"></i> Submit Integration Request
                            </button>
                        @endif
                    </div>
                </div>
@endsection

    @push('script')
                    <script>
                        // Direct event handler bypass for Next button
                        document.addEventListener('DOMContentLoaded', function() {
                            const nextButton = document.getElementById('next-step-button');
                            if (nextButton) {
                                nextButton.addEventListener('click', function(e) {
                                    console.log('Next button directly clicked');
                                    // Try direct method call as a fallback
                                    try {
                                    @this.nextStep();
                                    } catch (error) {
                                        console.error('Error calling nextStep:', error);
                                    }
                                });
                            }
                        });
                    </script>
    @endpush
