@if($internalIntegration)
    <div class="row">
        <div class="col-md-6">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <th width="40%">Middleware Connection</th>
                    <td>{{ $internalIntegration->middleware_connection ?: 'Not specified' }}</td>
                </tr>
                <tr>
                    <th>CMS Binding</th>
                    <td>{{ $internalIntegration->cms_binding ? 'Yes' : 'No' }}</td>
                </tr>
                @if($internalIntegration->cms_binding)
                    <tr>
                        <th>CMS Binding Details</th>
                        <td>{{ $internalIntegration->cms_binding_details }}</td>
                    </tr>
                @endif
                <tr>
                    <th>Security Classification</th>
                    <td>{{ $internalIntegration->security_classification }}</td>
                </tr>
                <tr>
                    <th>Responsible Team</th>
                    <td>{{ $internalIntegration->responsible_team }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            @if($internalIntegration->api_specifications)
                <h6>API Specifications</h6>
                <div class="bg-light p-3 mb-3 rounded pre-scrollable" style="max-height: 200px;">
                    <pre>{{ $internalIntegration->api_specifications }}</pre>
                </div>
            @endif

            @if(!empty($internalIntegration->features_supported))
                <h6>Features Supported</h6>
                <ul class="list-group mb-3">
                    @foreach($internalIntegration->features_supported as $feature)
                        <li class="list-group-item">{{ $feature }}</li>
                    @endforeach
                </ul>
            @endif

            @if($internalIntegration->system_dependencies)
                <h6>System Dependencies</h6>
                <p>{{ $internalIntegration->system_dependencies }}</p>
            @endif
        </div>
    </div>
@else
    <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle"></i>
        No internal integration details available for this request.
    </div>
@endif
