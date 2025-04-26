@if($externalIntegration)
    <div class="row">
        <div class="col-md-6">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th width="40%">Vendor</th>
                    <td>
                        @if($externalIntegration->is_new_vendor)
                            New Vendor (not in system)
                        @else
                            {{ $externalIntegration->vendor->name ?? 'Not specified' }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Connection Method</th>
                    <td>{{ $externalIntegration->connection_method ?: 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>Authentication Method</th>
                    <td>{{ $externalIntegration->authentication_method ?: 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>API Documentation URL</th>
                    <td>
                        @if($externalIntegration->api_documentation_url)
                            <a href="{{ $externalIntegration->api_documentation_url }}" target="_blank">
                                {{ $externalIntegration->api_documentation_url }}
                            </a>
                        @else
                            Not specified
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Data Formats</th>
                    <td>
                        @if(!empty($externalIntegration->data_formats))
                            @foreach($externalIntegration->data_formats as $format)
                                <span class="badge badge-info mr-1">{{ $format }}</span>
                            @endforeach
                        @else
                            Not specified
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Contract Expiration</th>
                    <td>{{ $externalIntegration->contract_expiration ? $externalIntegration->contract_expiration->format('Y-m-d') : 'Not specified' }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            @if($externalIntegration->network_requirements)
                <h6>Network Requirements</h6>
                <p>{{ $externalIntegration->network_requirements }}</p>
            @endif

            @if($externalIntegration->data_encryption_requirements)
                <h6>Data Encryption Requirements</h6>
                <p>{{ $externalIntegration->data_encryption_requirements }}</p>
            @endif

            @if($externalIntegration->rate_limiting)
                <h6>Rate Limiting</h6>
                <p>{{ $externalIntegration->rate_limiting }}</p>
            @endif

            @if($externalIntegration->sla_terms)
                <h6>SLA Terms</h6>
                <p>{{ $externalIntegration->sla_terms }}</p>
            @endif

            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Legal Approval</h6>
                    <p>
                        <span class="badge {{ $externalIntegration->legal_approval ? 'badge-success' : 'badge-secondary' }}">
                            {{ $externalIntegration->legal_approval ? 'Approved' : 'Pending' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Compliance Approval</h6>
                    <p>
                        <span class="badge {{ $externalIntegration->compliance_approval ? 'badge-success' : 'badge-secondary' }}">
                            {{ $externalIntegration->compliance_approval ? 'Approved' : 'Pending' }}
                        </span>
                    </p>
                </div>
            </div>

            @if($externalIntegration->sit_outcome)
                <h6>System Integration Testing Outcome</h6>
                <p>
                    <span class="badge
                        {{ $externalIntegration->sit_outcome === 'successful' ? 'badge-success' :
                          ($externalIntegration->sit_outcome === 'failed' ? 'badge-danger' : 'badge-warning') }}">
                        {{ ucfirst($externalIntegration->sit_outcome) }}
                    </span>
                </p>
            @endif

            @if($externalIntegration->business_impact)
                <h6>Business Impact</h6>
                <p>{{ $externalIntegration->business_impact }}</p>
            @endif

            @if($externalIntegration->issue_log)
                <h6>Issue Log</h6>
                <div class="bg-light p-3 rounded pre-scrollable" style="max-height: 150px;">
                    <pre>{{ $externalIntegration->issue_log }}</pre>
                </div>
            @endif
        </div>
    </div>
@else
    <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle"></i>
        No external integration details available for this request.
    </div>
@endif
