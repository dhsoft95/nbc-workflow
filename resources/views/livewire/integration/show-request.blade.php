<div>
    <!-- Success & Error Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Integration Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Integration Request: {{ $integration->name }}</h1>

        <div>
            <a href="{{ route('integrations.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- SLA Status Indicator -->
    @livewire('integration.sla-status-indicator', ['integration' => $integration])

    <!-- Integration Status Card -->
    <div class="card mb-4">
        <div class="card-header" style="background-color: #152755; color: white;">
            <h5 class="mb-0">Request Status</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h5>Current Status:
                        <span class="badge
                        @if(in_array($integration->status, ['approved', 'completed']))
                            badge-success
                        @elseif(in_array($integration->status, ['rejected', 'returned']))
                            badge-danger
                        @else
                            badge-info
                        @endif
                        ">
                            {{ ucwords(str_replace('_', ' ', $integration->status)) }}
                        </span>
                    </h5>
                    <p class="text-muted">
                        @if($currentStage)
                            This integration is currently awaiting {{ ucwords(str_replace('_', ' ', $currentStage)) }} review.
                        @elseif($integration->status === 'approved')
                            This integration has been fully approved.
                        @elseif($integration->status === 'rejected')
                            This integration has been rejected.
                        @elseif($integration->status === 'returned')
                            This integration has been returned for revisions.
                        @else
                            Status: {{ ucwords(str_replace('_', ' ', $integration->status)) }}
                        @endif
                    </p>
                </div>
                <div class="col-md-5">
                    <div class="d-flex justify-content-end">
                        @if($canApprove)
                            <button wire:click="approve" class="btn btn-success mr-2">
                                <i class="fa fa-check"></i> Approve
                            </button>

                            <button wire:click="reject" class="btn btn-danger mr-2">
                                <i class="fa fa-times"></i> Reject
                            </button>

                            <div class="dropdown">
                                <button class="btn btn-warning dropdown-toggle" type="button" id="returnDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-reply"></i> Return
                                </button>
                                <div class="dropdown-menu" aria-labelledby="returnDropdown">
                                    <a class="dropdown-item" href="#" wire:click.prevent="return('request')">Return to Requester</a>
                                    @if(in_array($integration->status, ['idi_approval', 'security_approval', 'infrastructure_approval']))
                                        <a class="dropdown-item" href="#" wire:click.prevent="return('app_owner')">Return to App Owner</a>
                                    @endif
                                    @if(in_array($integration->status, ['security_approval', 'infrastructure_approval']))
                                        <a class="dropdown-item" href="#" wire:click.prevent="return('idi')">Return to IDI</a>
                                    @endif
                                    @if($integration->status === 'infrastructure_approval')
                                        <a class="dropdown-item" href="#" wire:click.prevent="return('security')">Return to Security</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($canApprove)
                <div class="form-group mt-3">
                    <label for="comment">Comment:</label>
                    <textarea wire:model.defer="comment" class="form-control" id="comment" rows="3" placeholder="Add your comments here..."></textarea>
                </div>
            @endif
        </div>
    </div>

    <!-- Integration Details Card -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">General Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <tbody>
                        <tr>
                            <th width="40%">Name</th>
                            <td>{{ $integration->name }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>{{ ucfirst($integration->type) }} Integration</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $integration->department }}</td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td>
                                    <span class="badge
                                        @if($integration->priority === 'high')
                                            badge-danger
                                        @elseif($integration->priority === 'medium')
                                            badge-warning
                                        @else
                                            badge-info
                                        @endif">
                                        {{ ucfirst($integration->priority) }}
                                    </span>
                            </td>
                        </tr>
                        @if($integration->priority === 'high')
                            <tr>
                                <th>Priority Justification</th>
                                <td>{{ $integration->priority_justification }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Estimated Timeline</th>
                            <td>{{ $integration->estimated_timeline ? $integration->estimated_timeline->format('Y-m-d') : 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{ $integration->createdBy->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Created On</th>
                            <td>{{ $integration->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Purpose & Requirements</h5>
                </div>
                <div class="card-body">
                    <h6>Purpose</h6>
                    <p>{{ $integration->purpose }}</p>

                    @if($integration->resource_requirements)
                        <h6>Resource Requirements</h6>
                        <p>{{ $integration->resource_requirements }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Type-specific details -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">{{ ucfirst($integration->type) }} Integration Details</h5>
        </div>
        <div class="card-body">
            @if($integration->isInternal())
                @include('integration.partials.internal-details', ['internalIntegration' => $integration->internalIntegration])
            @elseif($integration->isExternal())
                @include('integration.partials.external-details', ['externalIntegration' => $integration->externalIntegration])
            @endif
        </div>
    </div>

    <!-- Attachments -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Attachments</h5>
        </div>
        <div class="card-body">
            @if($integration->attachments->where('type', '!=', 'metadata')->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                        <tr>
                            <th>Type</th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Uploaded by</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($integration->attachments->where('type', '!=', 'metadata') as $attachment)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $attachment->type)) }}</td>
                                <td>{{ $attachment->original_filename }}</td>
                                <td>{{ number_format($attachment->size / 1024, 2) }} KB</td>
                                <td>{{ $attachment->uploader->name ?? 'Unknown' }}</td>
                                <td>{{ $attachment->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ Storage::url($attachment->path) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">No attachments available for this integration.</p>
            @endif
        </div>
    </div>

    <!-- Approval History -->

    <!-- SLA History -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">SLA Tracking History</h5>
        </div>
        <div class="card-body">
            @php
                $slaHistories = $integration->approvalHistories()->orderBy('created_at')->get();
                $previousStage = null;
                $stageStartTime = null;
            @endphp

            @if($slaHistories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                        <tr>
                            <th>Stage</th>
                            <th>Started</th>
                            <th>Completed</th>
                            <th>Duration</th>
                            <th>SLA Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($slaHistories as $key => $history)
                            @php
                                // Calculate SLA timing
                                if ($previousStage != $history->stage) {
                                    // New stage started
                                    if ($previousStage) {
                                        // Output the previous stage
                                        $endTime = $history->created_at;
                                        $duration = $stageStartTime->diff($endTime);
                                        $durationHours = $stageStartTime->diffInHours($endTime);

                                        // Get SLA config for previous stage
                                        $slaConfig = App\Models\SlaConfiguration::where('stage', $previousStage)->first();
                                        $slaStatus = 'normal';

                                        if ($slaConfig) {
                                            if ($durationHours >= $slaConfig->critical_hours) {
                                                $slaStatus = 'critical';
                                            } elseif ($durationHours >= $slaConfig->warning_hours) {
                                                $slaStatus = 'warning';
                                            }
                                        }

                                        echo '<tr>';
                                        echo '<td><span class="badge badge-info">' . ucwords(str_replace('_', ' ', $previousStage)) . '</span></td>';
                                        echo '<td>' . $stageStartTime->format('Y-m-d H:i') . '</td>';
                                        echo '<td>' . $endTime->format('Y-m-d H:i') . '</td>';

                                        // Format duration
                                        if ($duration->days > 0) {
                                            echo '<td>' . $duration->days . ' days, ' . $duration->h . ' hours</td>';
                                        } else {
                                            echo '<td>' . $duration->h . ' hours, ' . $duration->i . ' minutes</td>';
                                        }

                                        // SLA status
                                        if ($slaStatus === 'critical') {
                                            echo '<td><span class="badge badge-danger">Critical</span></td>';
                                        } elseif ($slaStatus === 'warning') {
                                            echo '<td><span class="badge badge-warning">Warning</span></td>';
                                        } else {
                                            echo '<td><span class="badge badge-success">Within SLA</span></td>';
                                        }

                                        echo '</tr>';
                                    }

                                    // Start new stage
                                    $previousStage = $history->stage;
                                    $stageStartTime = $history->created_at;
                                }

                                // Check if this is the last record
                                if ($key === $slaHistories->count() - 1 && $integration->status !== 'approved' && $integration->status !== 'rejected') {
                                    // Current ongoing stage
                                    $now = now();
                                    $duration = $stageStartTime->diff($now);
                                    $durationHours = $stageStartTime->diffInHours($now);

                                    // Get SLA config
                                    $slaConfig = App\Models\SlaConfiguration::where('stage', $history->stage)->first();
                                    $slaStatus = 'normal';

                                    if ($slaConfig) {
                                        if ($durationHours >= $slaConfig->critical_hours) {
                                            $slaStatus = 'critical';
                                        } elseif ($durationHours >= $slaConfig->warning_hours) {
                                            $slaStatus = 'warning';
                                        }
                                    }

                                    echo '<tr class="table-active">';
                                    echo '<td><span class="badge badge-info">' . ucwords(str_replace('_', ' ', $history->stage)) . '</span></td>';
                                    echo '<td>' . $stageStartTime->format('Y-m-d H:i') . '</td>';
                                    echo '<td><i>In progress</i></td>';

                                    // Format duration
                                    if ($duration->days > 0) {
                                        echo '<td>' . $duration->days . ' days, ' . $duration->h . ' hours <i>(ongoing)</i></td>';
                                    } else {
                                        echo '<td>' . $duration->h . ' hours, ' . $duration->i . ' minutes <i>(ongoing)</i></td>';
                                    }

                                    // SLA status
                                    if ($slaStatus === 'critical') {
                                        echo '<td><span class="badge badge-danger">Critical</span></td>';
                                    } elseif ($slaStatus === 'warning') {
                                        echo '<td><span class="badge badge-warning">Warning</span></td>';
                                    } else {
                                        echo '<td><span class="badge badge-success">Within SLA</span></td>';
                                    }

                                    echo '</tr>';
                                }
                            @endphp
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fa fa-info-circle"></i>
                    <strong>Note:</strong> SLA tracking shows how long each approval stage took and whether it met the defined Service Level Agreement thresholds.
                </div>
            @else
                <p class="text-muted">No SLA tracking history available for this integration.</p>
            @endif
        </div>
    </div>

    @if($debug)
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Debug Information</h5>
            </div>
            <div class="card-body">
                <h6>Current Stage: {{ $currentStage ?? 'None' }}</h6>
                <h6>Can Approve: {{ $canApprove ? 'Yes' : 'No' }}</h6>
                <h6>Integration Status: {{ $integration->status }}</h6>
                <h6>User Permissions:</h6>
                <ul>
                    @foreach(auth()->user()->getAllPermissions() as $permission)
                        <li>{{ $permission->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
