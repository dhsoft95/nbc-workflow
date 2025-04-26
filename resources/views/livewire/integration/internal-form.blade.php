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
                <span class="badge {{ $stepCompleted[1] ? 'badge-success' : 'badge-secondary' }}">1</span> 📝 General Info
            </button>
            <button type="button" class="btn btn-link {{ !$stepCompleted[1] ? 'disabled' : '' }}"
                    wire:click="goToStep(2)" {{ !$stepCompleted[1] ? 'disabled' : '' }}>
                <span class="badge {{ $stepCompleted[2] ? 'badge-success' : 'badge-secondary' }}">2</span> 🔌 Integration Details
            </button>
            <button type="button" class="btn btn-link {{ !$stepCompleted[2] ? 'disabled' : '' }}"
                    wire:click="goToStep(3)" {{ !$stepCompleted[2] ? 'disabled' : '' }}>
                <span class="badge {{ $stepCompleted[3] ? 'badge-success' : 'badge-secondary' }}">3</span> 🔍 Review
            </button>
        </div>
    </div>


    <form wire:submit.prevent="save">
        <!-- Step 1: General Information -->
        <div class="card mb-4 {{ $currentStep != 1 ? 'd-none' : '' }}">
            <div class="card-header  text-white" style="background-color: #152755;">
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

        <!-- Step 2: Integration Details -->
        <div class="card mb-4 {{ $currentStep != 2 ? 'd-none' : '' }}">
            <div class="card-header  text-white" style="background-color: #152755;">
                <h5 class="mb-0">Step 2: Internal Integration Details</h5>
            </div>
            <div class="card-body">
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
            </div>
        </div>

        <!-- Step 3: Review -->
        <div class="card mb-4 {{ $currentStep != 3 ? 'd-none' : '' }}">
            <div class="card-header  text-white" style="background-color: #152755;">
                <h5 class="mb-0">Step 3: Review & Submit</h5>
            </div>
            <div class="card-body">
                <h5>General Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%">Integration Type</th>
                        <td>Internal</td>
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
                        <td>{{ $system_dependencies }}</td>
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
                    <button type="button" class="btn btn-primary" id="next-step-button" onclick="handleNextClick()">
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

    <!-- JavaScript for button handling -->
    <script>
        // Fix for Next button click handling
        function handleNextClick() {
            console.log('Next button clicked via onclick handler');

            // Try different methods to trigger the nextStep function
            try {
                // Method 1: Direct call to nextStep
            @this.nextStep();

                // Method 2: If Method 1 fails, try with setTimeout
                setTimeout(function() {
                    console.log('Trying delayed nextStep call');
                @this.nextStep();
                }, 50);
            } catch (error) {
                console.error('Error calling nextStep:', error);
            }
        }

        // Debug logging
        function debugLog(message, data = null) {
            if (document.getElementById('debug-messages')) {
                const timestamp = new Date().toLocaleTimeString();
                const msgContainer = document.createElement('div');
                msgContainer.innerHTML = `<span class="text-muted">[${timestamp}]</span> <strong>${message}</strong>`;

                if (data) {
                    const dataStr = typeof data === 'object' ? JSON.stringify(data) : data;
                    const dataContainer = document.createElement('pre');
                    dataContainer.className = 'small bg-light p-1 mt-1 mb-2';
                    dataContainer.textContent = dataStr;
                    msgContainer.appendChild(dataContainer);
                }

                document.getElementById('debug-messages').appendChild(msgContainer);
                console.log(`[${timestamp}] ${message}`, data || '');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            debugLog('Internal form loaded');

            // Show processing indicator when submitting the form
            const submitButton = document.getElementById('submit-button');
            if (submitButton) {
                submitButton.addEventListener('click', function() {
                    debugLog('Submit button clicked');
                    document.getElementById('form-processing-indicator').classList.remove('d-none');
                });
            }
        });

        // Auto-save form data every 2 minutes
        document.addEventListener('livewire:load', function() {
            // Auto-save functionality
            setInterval(function() {
                debugLog('Auto-save triggered');
            @this.emit('saveProgress');
            }, 120000); // 120000 ms = 2 minutes
        });
    </script>
</div>
