<div id="left-sidebar" class="sidebar">
    <button type="button" class="btn-toggle-offcanvas"><i class="fa fa-arrow-left"></i></button>
    <div class="sidebar-scroll">
        <!-- User Profile Section -->
        <div class="user-account">
            <img src="{{ asset('assets/images/user.png') }}" class="rounded-circle user-photo" alt="User Profile Picture">
            <div class="dropdown">
                <span>Welcome,</span>
                <a href="javascript:void(0);" class="dropdown-toggle user-name" data-toggle="dropdown"><strong>{{ Auth::user()->name ?? 'Guest' }}</strong></a>
                <ul class="dropdown-menu dropdown-menu-right account">
                    <li><a href="{{ route('profile.show') }}"><i class="fa fa-user"></i>My Profile</a></li>
                    <li><a href="{{ route('profile.edit') }}"><i class="fa fa-pencil"></i>Edit Profile</a></li>
                    <li><a href="{{ route('password.change') }}"><i class="fa fa-lock"></i>Change Password</a></li>
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off"></i>Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
            <hr>

            <!-- Integration Stats Overview -->
            <div class="stats-overview">
                <div class="row list-unstyled mb-0">
                    <div class="col-4 text-center">
                        <i class="fa fa-database" style="font-size:24px; color:#152755; margin-bottom:5px;"></i>
                        <small style="display:block;">Total</small>
                        <h6>{{ App\Models\Integration::count() }}</h6>
                    </div>
                    <div class="col-4 text-center">
                        <i class="fa fa-clock-o" style="font-size:24px; color:#152755; margin-bottom:5px;"></i>
                        <small style="display:block;">Pending</small>
                        <h6>{{ App\Models\Integration::whereIn('status', ['app_owner_approval', 'idi_approval', 'security_approval', 'infrastructure_approval'])->count() }}</h6>
                    </div>
                    <div class="col-4 text-center">
                        <i class="fa fa-check-circle" style="font-size:24px; color:#152755; margin-bottom:5px;"></i>
                        <small style="display:block;">Approved</small>
                        <h6>{{ App\Models\Integration::where('status', 'approved')->count() }}</h6>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" style="background-color: #152755; border-bottom: none;">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#menu" style="color: #fff; font-size: 20px;">
                    <i class="fa fa-list"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#notifications" style="color: #fff; font-size: 20px;">
                    <i class="fa fa-bell"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#setting" style="color: #fff; font-size: 20px;">
                    <i class="fa fa-cog"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#question" style="color: #fff; font-size: 20px;">
                    <i class="fa fa-question"></i>
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content padding-0">
            <!-- Main Navigation Menu -->
            <div class="tab-pane active" id="menu">
                <nav id="left-sidebar-nav" class="sidebar-nav">
                    <ul class="metismenu li_animation_delay">
                        <!-- Dashboard -->
                        <li class="{{ Request::is('dashboard*') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="fa fa-dashboard"></i><span>Dashboard</span>
                            </a>
                        </li>

                        <!-- Integration Management -->
                        <li class="{{ request()->routeIs('integrations.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="has-arrow">
                                <i class="fa fa-exchange"></i><span>Integrations</span>
                            </a>
                            <ul>
                                <li class="{{ request()->routeIs('integrations.create') ? 'active' : '' }}">
                                    <a data-toggle="modal" data-target="#integrationTypeModal" href=""><i class="fa fa-plus-circle"></i> Create New</a>
                                </li>
                                <li class="{{ request()->routeIs('integrations.my') ? 'active' : '' }}">
                                    <a href="{{ route('integrations.my') }}"><i class="fa fa-user-circle"></i> My Requests</a>
                                </li>
                                @if(auth()->user()->hasAnyRole(['app_owner', 'idi_team', 'security_team', 'infrastructure_team']))
                                    <li class="{{ request()->routeIs('integrations.pending') ? 'active' : '' }}">
                                        <a href="{{ route('integrations.pending') }}"><i class="fa fa-clock-o"></i> Pending Approval</a>
                                    </li>
                                @endif
                                <li class="{{ request()->routeIs('integrations.index') ? 'active' : '' }}">
                                    <a href="{{ route('integrations.index') }}"><i class="fa fa-list"></i> All Integrations</a>
                                </li>
                            </ul>
                        </li>

                        <!-- SLA Configuration (Only for Administrator) -->
                        @role('administrator')
                        <li class="{{ Request::is('admin/sla-configurations*') ? 'active' : '' }}">
                            <a href="{{ route('admin.sla-configurations.index') }}">
                                <i class="fa fa-hourglass-half"></i><span>SLA Configuration</span>
                            </a>
                        </li>
                        @endrole

                        <!-- Vendor Management -->
                        @role('administrator|vendor_manager')
                        <li class="{{ Request::is('admin/vendors*') ? 'active' : '' }}">
                            <a href="{{ route('admin.vendors.index') }}">
                                <i class="fa fa-building"></i><span>Vendor Management</span>
                            </a>
                        </li>
                        @endrole

                        <!-- User & Role Management -->
                        @can('manage users')
                            <li class="{{ Request::is('admin/users*') || Request::is('admin/roles*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="has-arrow">
                                    <i class="fa fa-users"></i><span>User Management</span>
                                </a>
                                <ul>
                                    <li class="{{ Request::is('admin/users') ? 'active' : '' }}">
                                        <a href="{{ route('admin.users.index') }}"><i class="fa fa-user"></i> Manage Users</a>
                                    </li>
                                    @can('manage roles')
                                        <li class="{{ Request::is('admin/roles') ? 'active' : '' }}">
                                            <a href="{{ route('roles.index') }}"><i class="fa fa-key"></i> Manage Roles</a>
                                        </li>
                                        <li class="{{ Request::is('admin/users/roles') ? 'active' : '' }}">
                                            <a href="{{ route('users.roles') }}"><i class="fa fa-user-plus"></i> User Roles</a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                        @endcan

                        <!-- Configuration -->
                        @can('manage configuration')
                            <li class="{{ Request::is('admin/configurations*') ? 'active' : '' }}">
                                <a href="{{ route('admin.configurations.index') }}">
                                    <i class="fa fa-cogs"></i><span>Configuration</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </nav>
            </div>

            <!-- Notifications Tab -->
            <div class="tab-pane" id="notifications">
                <div class="p-3">
                    <h6 class="mb-3">Recent Integration Activity</h6>

                    @php
                        // Get latest integration requests requiring attention
                        $pendingIntegrations = App\Models\Integration::whereIn('status',
                            ['app_owner_approval', 'idi_approval', 'security_approval', 'infrastructure_approval'])
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    <ul class="list-unstyled feeds_widget mb-0">
                        @forelse($pendingIntegrations as $integration)
                            <li class="border-bottom pb-2 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="feeds-left mr-3">
                                        @if($integration->exceedsSlaCritical())
                                            <i class="fa fa-exclamation-circle text-danger fa-lg"></i>
                                        @elseif($integration->exceedsSlaWarning())
                                            <i class="fa fa-exclamation-triangle text-warning fa-lg"></i>
                                        @else
                                            <i class="fa fa-thumbtack text-info fa-lg"></i>
                                        @endif
                                    </div>
                                    <div class="feeds-body">
                                        <h6 class="mb-1">{{ $integration->name }}</h6>
                                        <small class="text-muted">Awaiting {{ str_replace('_', ' ', str_replace('_approval', '', $integration->status)) }} approval</small>
                                        <div class="text-muted">
                                            <small>{{ $integration->updated_at->diffForHumans() }}</small>
                                            @if($integration->exceedsSlaCritical())
                                                <span class="badge badge-danger ml-1">SLA Critical</span>
                                            @elseif($integration->exceedsSlaWarning())
                                                <span class="badge badge-warning ml-1">SLA Warning</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-3">
                                <i class="fa fa-check-circle text-success fa-2x mb-2"></i>
                                <p>No pending approvals</p>
                            </li>
                        @endforelse
                    </ul>

                    @if(count($pendingIntegrations) > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('integrations.pending') }}" class="btn btn-primary btn-sm">View All Pending Approvals</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="tab-pane" id="setting">
                <div class="p-3">
                    <h6 class="mb-3">Theme Customization</h6>

                    <!-- Theme Color Options -->
                    <div class="mb-4">
                        <label class="d-block mb-2">Color Theme</label>
                        <ul class="choose-skin list-unstyled d-flex flex-wrap">
                            <li data-theme="purple" class="mr-2 mb-2"><div class="purple"></div></li>
                            <li data-theme="blue" class="mr-2 mb-2"><div class="blue"></div></li>
                            <li data-theme="cyan" class="active mr-2 mb-2"><div class="cyan"></div></li>
                            <li data-theme="green" class="mr-2 mb-2"><div class="green"></div></li>
                            <li data-theme="orange" class="mr-2 mb-2"><div class="orange"></div></li>
                            <li data-theme="blush" class="mr-2 mb-2"><div class="blush"></div></li>
                            <li data-theme="red" class="mr-2 mb-2"><div class="red"></div></li>
                        </ul>
                    </div>

                    <!-- Font Options -->
                    <div class="mb-4">
                        <label class="d-block mb-2">Font Family</label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" class="custom-control-input" name="font" value="font-nunito" id="font-nunito" checked="">
                            <label class="custom-control-label" for="font-nunito">Nunito Sans</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" class="custom-control-input" name="font" value="font-ubuntu" id="font-ubuntu">
                            <label class="custom-control-label" for="font-ubuntu">Ubuntu</label>
                        </div>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" class="custom-control-input" name="font" value="font-raleway" id="font-raleway">
                            <label class="custom-control-label" for="font-raleway">Raleway</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" name="font" value="font-IBMplex" id="font-IBMplex">
                            <label class="custom-control-label" for="font-IBMplex">IBM Plex Sans</label>
                        </div>
                    </div>

                    <!-- Display Options -->
                    <div class="mb-4">
                        <label class="d-block mb-2">Display Options</label>
                        <div class="d-flex align-items-center mb-2">
                            <label class="toggle-switch theme-switch mr-3">
                                <input type="checkbox">
                                <span class="toggle-switch-slider"></span>
                            </label>
                            <span>Dark Mode</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <label class="toggle-switch theme-rtl mr-3">
                                <input type="checkbox">
                                <span class="toggle-switch-slider"></span>
                            </label>
                            <span>RTL Layout</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="toggle-switch theme-high-contrast mr-3">
                                <input type="checkbox">
                                <span class="toggle-switch-slider"></span>
                            </label>
                            <span>High Contrast</span>
                        </div>
                    </div>

                    <!-- Notification Preferences -->
                    <div>
                        <label class="d-block mb-2">Notification Settings</label>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="allow-notifications" checked>
                            <label class="custom-control-label" for="allow-notifications">Enable Notifications</label>
                        </div>
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="email-notifications">
                            <label class="custom-control-label" for="email-notifications">Email Notifications</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="browser-notifications">
                            <label class="custom-control-label" for="browser-notifications">Browser Notifications</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help & FAQ Tab -->
            <div class="tab-pane" id="question">
                <div class="p-3">
                    <h6 class="mb-3">Frequently Asked Questions</h6>

                    <div class="accordion" id="faqAccordion">
                        <div class="card mb-2">
                            <div class="card-header p-2" id="faqHeading1">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse1" aria-expanded="false" aria-controls="faqCollapse1">
                                        What is SLA tracking?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse1" class="collapse" aria-labelledby="faqHeading1" data-parent="#faqAccordion">
                                <div class="card-body">
                                    SLA (Service Level Agreement) tracking monitors the time spent in each approval stage to ensure timely processing of integration requests.
                                </div>
                            </div>
                        </div>

                        <div class="card mb-2">
                            <div class="card-header p-2" id="faqHeading2">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                        How do I create a new integration?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse2" class="collapse" aria-labelledby="faqHeading2" data-parent="#faqAccordion">
                                <div class="card-body">
                                    Click on "Integrations" in the sidebar, then select "Create New" and choose between Internal or External integration types.
                                </div>
                            </div>
                        </div>

                        <div class="card mb-2">
                            <div class="card-header p-2" id="faqHeading3">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                        What do the SLA warning colors mean?
                                    </button>
                                </h2>
                            </div>
                            <div id="faqCollapse3" class="collapse" aria-labelledby="faqHeading3" data-parent="#faqAccordion">
                                <div class="card-body">
                                    <ul class="mb-0 pl-3">
                                        <li><span class="badge badge-success">Normal</span> - Within SLA time limits</li>
                                        <li><span class="badge badge-warning">Warning</span> - Approaching SLA threshold</li>
                                        <li><span class="badge badge-danger">Critical</span> - Exceeded SLA threshold</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
{{--                            <a href="{{ route('help.faq') }}" class="btn btn-sm btn-primary">View All FAQs</a>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for selecting integration type -->
<div class="modal fade" id="integrationTypeModal" tabindex="-1" role="dialog" aria-labelledby="integrationTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="integrationTypeModalLabel">Select Integration Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-center mb-3">
                        <a href="{{ route('integrations.internal.create') }}" class="btn btn-primary btn-block">Internal Integration</a>
                    </div>
                    <div class="col-md-6 text-center mb-3">
                        <a href="{{ route('integrations.external.create') }}" class="btn btn-secondary btn-block">External Integration</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // This script ensures that Bootstrap tabs work properly
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap tabs
        $('.nav-tabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
