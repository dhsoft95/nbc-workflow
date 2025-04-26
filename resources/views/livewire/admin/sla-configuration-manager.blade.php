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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">SLA Configuration</h1>
    </div>

    <div class="row">
        <!-- SLA Configuration Card -->
        <div class="col-md-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #152755; color: white;">
                    <h6 class="m-0 font-weight-bold">Service Level Agreement (SLA) Configurations</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                            <tr>
                                <th>Stage</th>
                                <th>Warning Hours</th>
                                <th>Critical Hours</th>
                                <th>Include Weekends</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($slaConfigurations as $config)
                                <tr>
                                    <td>
                                        @if($config->stage == 'request')
                                            Initial Request
                                        @elseif($config->stage == 'app_owner')
                                            App Owner Approval
                                        @elseif($config->stage == 'idi')
                                            IDI Team Approval
                                        @elseif($config->stage == 'security')
                                            Security Team Approval
                                        @elseif($config->stage == 'infrastructure')
                                            Infrastructure Team Approval
                                        @else
                                            {{ ucfirst($config->stage) }}
                                        @endif
                                    </td>
                                    <td>{{ $config->warning_hours }} hours</td>
                                    <td>{{ $config->critical_hours }} hours</td>
                                    <td>{{ $config->include_weekends ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <button wire:click="editSla({{ $config->id }})" class="btn btn-sm btn-blue" style="background-color: #dc3545; color: white;">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> SLA configurations determine when notifications are sent for approval stages that exceed expected timeframes.
                    </div>
                </div>
            </div>
        </div>

        <!-- Holiday Configuration Card -->
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #152755; color: white;">
                    <h6 class="m-0 font-weight-bold">Holidays Configuration</h6>
                </div>
                <div class="card-body">
                    <button wire:click="createHoliday" class="btn btn-blue mb-3" style="background-color: #dc3545; color: white;">
                        <i class="fa fa-plus"></i> Add New Holiday
                    </button>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                            <tr>
                                <th>Holiday Name</th>
                                <th>Date</th>
                                <th>Recurring</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($holidays as $holiday)
                                <tr>
                                    <td>{{ $holiday->name }}</td>
                                    <td>{{ $holiday->date->format('M d, Y') }}</td>
                                    <td>{{ $holiday->recurring ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <button wire:click="editHoliday({{ $holiday->id }})" class="btn btn-sm btn-blue" style="background-color: #007bff; color: white;">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $holiday->id }})" class="btn btn-sm btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $holidays->links() }}
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> Holidays are excluded from SLA calculations. Recurring holidays repeat every year on the same date.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit SLA Modal -->
    @if($isEditingSla)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #007bff; color: white;">
                        <h5 class="modal-title">Edit SLA Configuration</h5>
                        <button type="button" class="close text-white" wire:click="cancel">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Stage</label>
                            <input type="text" class="form-control" value="{{ ucwords(str_replace('_', ' ', $slaConfigurations->where('id', $editSlaId)->first()->stage ?? '')) }}" disabled>
                        </div>

                        <div class="form-group">
                            <label for="warningHours">Warning Hours</label>
                            <input type="number" class="form-control" id="warningHours" wire:model="warningHours" min="1" required>
                            <small class="form-text text-muted">Send warning notification after this many hours</small>
                            @error('warningHours') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="criticalHours">Critical Hours</label>
                            <input type="number" class="form-control" id="criticalHours" wire:model="criticalHours" min="1" required>
                            <small class="form-text text-muted">Send critical notification after this many hours</small>
                            @error('criticalHours') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="includeWeekends" wire:model="includeWeekends">
                                <label class="custom-control-label" for="includeWeekends">Include Weekends in SLA Calculations</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
                        <button type="button" class="btn btn-blue" style="background-color: #007bff; color: white;" wire:click="updateSla">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Holiday Modal -->
    @if($isEditingHoliday)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #007bff; color: white;">
                        <h5 class="modal-title">{{ $editHolidayId ? 'Edit Holiday' : 'Add New Holiday' }}</h5>
                        <button type="button" class="close text-white" wire:click="cancel">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="holidayName">Holiday Name</label>
                            <input type="text" class="form-control" id="holidayName" wire:model="holidayName" required>
                            @error('holidayName') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="holidayDate">Date</label>
                            <input type="date" class="form-control" id="holidayDate" wire:model="holidayDate" required>
                            @error('holidayDate') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="holidayRecurring" wire:model="holidayRecurring">
                                <label class="custom-control-label" for="holidayRecurring">Recurring Every Year</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancel">Cancel</button>
                        <button type="button" class="btn btn-blue" style="background-color: #007bff; color: white;" wire:click="saveHoliday">
                            {{ $editHolidayId ? 'Update Holiday' : 'Add Holiday' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($isConfirmingDelete)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Delete Holiday</h5>
                        <button type="button" class="close text-white" wire:click="cancelDelete">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @if($deleteHolidayId && ($holiday = $holidays->firstWhere('id', $deleteHolidayId)))
                            <p>Are you sure you want to delete the holiday: <strong>{{ $holiday->name }}</strong>?</p>
                        @else
                            <p>Are you sure you want to delete this holiday?</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                        <button type="button" class="btn btn-danger" wire:click="deleteHoliday">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
