<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Create New Integration</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4 h-100">
                                <div class="card-body text-center">
                                    <i class="fa fa-building fa-4x mb-3 text-primary"></i>
                                    <h5 class="card-title">Internal Integration</h5>
                                    <p class="card-text">Create an integration with internal systems and services within your organization.</p>
                                    <ul class="list-unstyled text-left">
                                        <li><i class="fa fa-check text-success mr-2"></i> Connect with middleware</li>
                                        <li><i class="fa fa-check text-success mr-2"></i> Integrate with CMS</li>
                                        <li><i class="fa fa-check text-success mr-2"></i> Establish secure connections</li>
                                    </ul>
                                    <a href="{{ route('integrations.internal.create') }}" class="btn btn-primary mt-3">
                                        <i class="fa fa-plus-circle mr-2"></i>Create Internal Integration
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4 h-100">
                                <div class="card-body text-center">
                                    <i class="fa fa-globe fa-4x mb-3 text-primary"></i>
                                    <h5 class="card-title">External Integration</h5>
                                    <p class="card-text">Create an integration with external vendors, APIs, and third-party services.</p>
                                    <ul class="list-unstyled text-left">
                                        <li><i class="fa fa-check text-success mr-2"></i> Connect with external vendors</li>
                                        <li><i class="fa fa-check text-success mr-2"></i> Set up API connections</li>
                                        <li><i class="fa fa-check text-success mr-2"></i> Manage contracts and SLAs</li>
                                    </ul>
                                    <a href="{{ route('integrations.external.create') }}" class="btn btn-primary mt-3">
                                        <i class="fa fa-plus-circle mr-2"></i>Create External Integration
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">Not sure which type of integration you need? <a href="#">View documentation</a> or <a href="#">contact support</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
