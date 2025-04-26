<div>
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
        <div class="progress" style="height: 20px; background-color: #fff;">
            <div class="progress-bar progress-bar-striped progress-bar-animated"
                 role="progressbar"
                 style="width: {{ ($currentStep / $totalSteps) * 100 }}%; background-color: #152755;">
                Step {{ $currentStep }} of {{ $totalSteps }}
            </div>
        </div>
    </div>

    <!-- Steps navigation -->
    <div class="step-navigation mb-4">
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-link {{ $currentStep == 1 ? 'disabled' : '' }}"
                    wire:click="goToStep(1)" {{ $currentStep == 1 ? 'disabled' : '' }}>
                <span class="badge {{ $stepCompleted[1] ? 'badge-success' : 'badge-secondary' }}">1</span>
                <i class="fa fa-info-circle" style="margin-right:5px;"></i> General Info
            </button>

            <button type="button" class="btn btn-link {{ !$stepCompleted[1] ? 'disabled' : '' }}"
                    wire:click="goToStep(2)" {{ !$stepCompleted[1] ? 'disabled' : '' }}>
                <span class="badge {{ $stepCompleted[2] ? 'badge-success' : 'badge-secondary' }}">2</span>
                <i class="fa fa-plug" style="margin-right:5px;"></i> Integration Details
            </button>

            <button type="button" class="btn btn-link {{ !$stepCompleted[2] ? 'disabled' : '' }}"
                    wire:click="goToStep(3)" {{ !$stepCompleted[2] ? 'disabled' : '' }}>
                <span class="badge {{ $stepCompleted[3] ? 'badge-success' : 'badge-secondary' }}">3</span>
                <i class="fa fa-paperclip" style="margin-right:5px;"></i> Attachments
            </button>

            <button type="button" class="btn btn-link {{ !$stepCompleted[3] ? 'disabled' : '' }}"
                    wire:click="goToStep(4)" {{ !$stepCompleted[3] ? 'disabled' : '' }}>
                <span class="badge {{ $stepCompleted[4] ? 'badge-success' : 'badge-secondary' }}">4</span>
                <i class="fa fa-check" style="margin-right:5px;"></i> Review
            </button>
        </div>
    </div>


    <form wire:submit.prevent="save">
        <!-- Step 1: General Information -->
        <div class="card mb-4 {{ $currentStep != 1 ? 'd-none' : '' }}">
            <div class="card-header bg- text-white" style="background-color: #152755;">
                <h5 class="mb-0">Step 1: General Information</h5>
            </div>
            <div class="card-body">
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

        <!-- Step 2: External Integration Details -->
        <div class="card mb-4 {{ $currentStep != 2 ? 'd-none' : '' }}">
            <div class="card-header text-white" style="background-color: #152755;">
                <h5 class="mb-0">Step 2: External Integration Details</h5>
            </div>
            <div class="card-body">
                <!-- Vendor Selection -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Vendor Information</h6>
                    </div>
                    <div class="card-body">
                        <!-- Vendor Type Selection -->
                        <div class="form-group mb-4">
                            <label class="d-block font-weight-bold">Vendor Type</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary {{ $vendor_selection == 'new' ? 'active' : '' }} flex-grow-1"
                                       wire:click="$set('vendor_selection', 'new')">
                                    <input type="radio" name="vendor_selection" value="new"
                                        {{ $vendor_selection == 'new' ? 'checked' : '' }}>
                                    <i class="fa fa-plus-circle mr-1"></i> New Vendor
                                </label>
                                <label class="btn btn-outline-primary {{ $vendor_selection == 'existing' ? 'active' : '' }} flex-grow-1"
                                       wire:click="$set('vendor_selection', 'existing')">
                                    <input type="radio" name="vendor_selection" value="existing"
                                        {{ $vendor_selection == 'existing' ? 'checked' : '' }}>
                                    <i class="fa fa-building mr-1"></i> Existing Vendor
                                </label>
                            </div>
                            @error('vendor_selection') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Existing Vendor Selection (always shown but disabled when "New Vendor" is selected) -->
                        <div class="form-group" id="existing-vendor-dropdown">
                            <label for="vendor_id">Select Vendor</label>
                            <select class="form-control @error('vendor_id') is-invalid @enderror" id="vendor_id"
                                    wire:model="vendor_id" {{ $vendor_selection == 'new' ? 'disabled' : '' }}>
                                <option value="">-- Select an existing vendor --</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                            @error('vendor_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="form-text text-muted">
                                {{ $vendor_selection == 'new' ? 'This field is disabled because you selected "New Vendor".' : 'Please select the vendor for this integration.' }}
                            </small>
                        </div>
                    </div>
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
            </div>
        </div>

        <!-- Step 3: File Uploads -->
        <div class="card mb-4 {{ $currentStep != 3 ? 'd-none' : '' }}">
            <div class="card-header text-white" style="background-color: #152755;">
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
            <div class="card-header text-white" style="background-color: #152755;">
                <h5 class="mb-0">Step 4: Review & Submit</h5>
            </div>
            <div class="card-body">
                <h5>General Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Integration Type</th>
                        <td>External</td>
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

                <!-- External Integration Review -->
                <h5 class="mt-4">External Integration Details</h5>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Vendor</th>
                        <td>
                            @if($vendor_selection === 'new')
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

                <!-- Terms and confirmation -->
                <div class="alert alert-info mt-4">
                    <p class="mb-0">
                        ⓘ
                        By submitting this integration request, you confirm that all the information provided is accurate to the best of your knowledge.
                        The request will be submitted for approval by the relevant stakeholders.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form navigation and submit buttons -->
        <div class="d-flex justify-content-between mb-5">
            <button type="button" class="btn btn-secondary" wire:click="previousStep" {{ $currentStep == 1 ? 'disabled' : '' }}>
                ← Previous
            </button>

            <div>
                @if($currentStep < $totalSteps)
                    <button type="button" class="btn btn-primary" wire:click="nextStep">
                        Next →
                    </button>
                @else
                    <button type="submit" class="btn btn-success" id="submit-button">
                        ✓ Submit Integration Request
                    </button>
                @endif
            </div>
        </div>
    </form>

    <!-- Loading indicator for form submission -->
    <div class="fixed-bottom text-center p-3 bg-white shadow d-none" id="form-processing-indicator">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Processing...</span>
        </div>
        <div class="mt-2">Processing your request, please wait...</div>
    </div>

    <!-- JavaScript for file uploads and vendor selection -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('External form loaded');

            // Show processing indicator when submitting the form
            const submitButton = document.getElementById('submit-button');
            if (submitButton) {
                submitButton.addEventListener('click', function() {
                    console.log('Submit button clicked');
                    document.getElementById('form-processing-indicator').classList.remove('d-none');
                });
            }

            // Custom file input label update with improved error handling
            function updateFileLabels() {
                document.querySelectorAll('.custom-file-input').forEach(function(input) {
                    input.addEventListener('change', function() {
                        try {
                            let fileName = this.files[0] ? this.files[0].name : 'Choose file';
                            let label = this.nextElementSibling;
                            if (label) {
                                label.textContent = fileName;
                                console.log('File input changed', { input: this.id, filename: fileName });
                            }
                        } catch (error) {
                            console.error('Error updating file label:', error);
                        }
                    });
                });
            }

            updateFileLabels();

            // Vendor selection handling - ensure dropdown is enabled/disabled correctly
            function updateVendorDropdown() {
                try {
                    const vendorSelection = document.querySelector('input[name="vendor_selection"]:checked')?.value || 'new';
                    const vendorDropdown = document.getElementById('vendor_id');

                    if (vendorDropdown) {
                        if (vendorSelection === 'new') {
                            vendorDropdown.disabled = true;
                            vendorDropdown.value = '';
                        } else {
                            vendorDropdown.disabled = false;
                        }
                        console.log('Vendor dropdown state updated:', { selection: vendorSelection, disabled: vendorDropdown.disabled });
                    }
                } catch (error) {
                    console.error('Error updating vendor dropdown:', error);
                }
            }

            // Call initially and after any Livewire updates
            updateVendorDropdown();

            // Re-attach handlers after Livewire updates
            document.addEventListener('livewire:update', function() {
                setTimeout(function() {
                    updateFileLabels();
                    updateVendorDropdown();
                }, 100);
            });
        });

        // Livewire hook for auto-save and vendor dropdown handling
        document.addEventListener('livewire:load', function() {
            // Handle vendor_selection changes - this complements the wire:click handlers
            Livewire.hook('message.processed', (message, component) => {
                // Toggle the vendor dropdown based on selection
                const vendorDropdown = document.getElementById('vendor_id');
                if (vendorDropdown) {
                    const isNewVendor = @this.vendor_selection === 'new';
                    vendorDropdown.disabled = isNewVendor;
                    console.log('Livewire update - vendor dropdown state:', {
                        disabled: vendorDropdown.disabled,
                        selection: @this.vendor_selection
                    });
                }
            });

            // Auto-save functionality
            setInterval(function() {
                console.log('Auto-save triggered');
            @this.emit('saveProgress');
            }, 120000); // 120000 ms = 2 minutes
        });
    </script>
</div>
