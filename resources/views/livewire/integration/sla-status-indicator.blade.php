<div class="sla-status-indicator">
    @if($stage)
        <div class="card mb-4">
            <div class="card-header {{ $status === 'normal' ? 'bg-info' : ($status === 'warning' ? 'bg-warning' : 'bg-danger') }} text-white">
                <h5 class="mb-0">
                    <i class="fa {{ $status === 'normal' ? 'fa-clock-o' : 'fa-exclamation-triangle' }}"></i>
                    SLA Status for {{ $this->stageName }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <strong>Time in current stage:</strong>
                            <span class="{{ $status === 'normal' ? '' : ($status === 'warning' ? 'text-warning' : 'text-danger') }}">
                                {{ number_format($hoursInStage, 1) }} hours
                            </span>
                        </p>

                        @if($slaConfig)
                            <p>
                                <strong>Warning threshold:</strong> {{ $slaConfig->warning_hours }} hours
                            </p>
                            <p>
                                <strong>Critical threshold:</strong> {{ $slaConfig->critical_hours }} hours
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6>SLA Progress</h6>
                        <div class="progress" style="height: 30px;">
                            <div
                                class="progress-bar progress-bar-striped
                                    {{ $status === 'normal' ? 'bg-info' : ($status === 'warning' ? 'bg-warning' : 'bg-danger') }}"
                                role="progressbar"
                                style="width: {{ $percentage }}%"
                                aria-valuenow="{{ $percentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                                {{ $percentage }}%
                            </div>
                        </div>

                        @if($status === 'warning')
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> This integration is approaching the SLA limit.
                            </div>
                        @elseif($status === 'critical')
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fa fa-exclamation-circle"></i>
                                <strong>Critical Alert:</strong> This integration has exceeded the SLA limit!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
