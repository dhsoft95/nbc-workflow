<div id="left-sidebar" class="sidebar">
    <button type="button" class="btn-toggle-offcanvas"><i class="fa fa-arrow-left"></i></button>
    <div class="sidebar-scroll">
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
        </div>

        <!-- User Status Section -->
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#menu">Menu</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#Chat"><i class="fa fa-comment"></i></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#setting"><i class="fa fa-cog"></i></a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#question"><i class="fa fa-question"></i></a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content padding-0">
            <div class="tab-pane active" id="menu">
                <nav id="left-sidebar-nav" class="sidebar-nav">
                    <ul class="metismenu li_animation_delay">
                        <li class="{{ Request::is('dashboard*') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="fa fa-home"></i><span>Dashboard</span>
                            </a>
                        </li>

                        <!-- Integration Module Navigation -->
                        <li class="">
                            <a href="javascript:void(0);" class="has-arrow"><i class="fa fa-plug"></i><span>Integrations</span></a>
                            <ul>
                                <li class="">
                                    <a href="javascript:void(0);" class="has-arrow"><i class="fa fa-file-text"></i> Requests</a>
                                    <ul>
                                        <li class="">
                                            <a href="#"><i class="fa fa-plus"></i> New Internal</a>
                                        </li>
                                        <li class="">
                                            <a href="#"><i class="fa fa-external-link"></i> New External</a>
                                        </li>
                                        <li class="">
                                            <a href="#"><i class="fa fa-list"></i> My Requests</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="">
                                    <a href="javascript:void(0);" class="has-arrow"><i class="fa fa-check-square"></i> Approvals</a>
                                    <ul>
                                        <li class="">
                                            <a href="#"><i class="fa fa-clock-o"></i> Pending My Approval</a>
                                        </li>
                                        <li class="">
                                            <a href="#"><i class="fa fa-history"></i> Approval History</a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="">
                                    <a href="javascript:void(0);" class="has-arrow"><i class="fa fa-bar-chart"></i> Reports</a>
                                    <ul>
                                        <li class="">
                                            <a href="#"><i class="fa fa-tasks"></i> Status Report</a>
                                        </li>
                                        <li class="">
                                            <a href="#"><i class="fa fa-clock-o"></i> Time Tracking</a>
                                        </li>
                                        <li class="">
                                            <a href="#"><i class="fa fa-line-chart"></i> Performance</a>
                                        </li>
                                    </ul>
                                </li>

                                @if(Auth::user()->hasRole('administrator'))
                                    <li class="">
                                        <a href="javascript:void(0);" class="has-arrow"><i class="fa fa-cogs"></i> Configuration</a>
                                        <ul>
                                            <li class="">
                                                <a href="#"><i class="fa fa-wpforms"></i> Form Fields</a>
                                            </li>
                                            <li class="">
                                                <a href="#"><i class="fa fa-sitemap"></i> Workflow</a>
                                            </li>
                                            <li class="">
                                                <a href="#"><i class="fa fa-tachometer"></i> SLA Settings</a>
                                            </li>
                                            <li class="">
                                                <a href="#"><i class="fa fa-building"></i> Vendor Management</a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        <!-- End Integration Module Navigation -->

                        <!-- Administration Section -->
                        @if(Auth::user()->hasRole('administrator'))
                            <li class="{{ Request::is('admin/roles*') || Request::is('admin/users/roles*') ? 'active' : '' }}">
                                <a href="javascript:void(0);" class="has-arrow"><i class="fa fa-shield"></i><span>Administration</span></a>
                                <ul>
                                    <!-- User Roles -->
                                    <li class="{{ Request::is('admin/users/roles*') ? 'active' : '' }}">
                                        <a href="{{ route('users.roles') }}">
                                            <i class="fa fa-users"></i> User Roles
                                        </a>
                                    </li>

                                    <!-- Roles Management -->
                                    <li class="{{ Request::is('admin/roles*') ? 'active' : '' }}">
                                        <a href="{{ route('roles.index') }}">
                                            <i class="fa fa-key"></i> Manage Roles
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <!-- End Administration Section -->
                    </ul>
                </nav>
            </div>
            <div class="tab-pane" id="Chat">
                <form>
                    <div class="input-group m-b-20">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Search...">
                    </div>
                </form>
                <div class="text-center py-4">
                    <p>This is a placeholder for the chat tab. You can customize this based on your requirements.</p>
                </div>
            </div>
            <div class="tab-pane" id="setting">
                <h6>Choose Skin</h6>
                <ul class="choose-skin list-unstyled">
                    <li data-theme="purple"><div class="purple"></div></li>
                    <li data-theme="blue"><div class="blue"></div></li>
                    <li data-theme="cyan" class="active"><div class="cyan"></div></li>
                    <li data-theme="green"><div class="green"></div></li>
                    <li data-theme="orange"><div class="orange"></div></li>
                    <li data-theme="blush"><div class="blush"></div></li>
                    <li data-theme="red"><div class="red"></div></li>
                </ul>

                <ul class="list-unstyled font_setting mt-3">
                    <li>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="font" value="font-nunito" checked="">
                            <span class="custom-control-label">Nunito Google Font</span>
                        </label>
                    </li>
                    <li>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="font" value="font-ubuntu">
                            <span class="custom-control-label">Ubuntu Font</span>
                        </label>
                    </li>
                    <li>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="font" value="font-raleway">
                            <span class="custom-control-label">Raleway Google Font</span>
                        </label>
                    </li>
                    <li>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="font" value="font-IBMplex">
                            <span class="custom-control-label">IBM Plex Google Font</span>
                        </label>
                    </li>
                </ul>

                <ul class="list-unstyled mt-3">
                    <li class="d-flex align-items-center mb-2">
                        <label class="toggle-switch theme-switch">
                            <input type="checkbox">
                            <span class="toggle-switch-slider"></span>
                        </label>
                        <span class="ml-3">Enable Dark Mode!</span>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <label class="toggle-switch theme-rtl">
                            <input type="checkbox">
                            <span class="toggle-switch-slider"></span>
                        </label>
                        <span class="ml-3">Enable RTL Mode!</span>
                    </li>
                    <li class="d-flex align-items-center mb-2">
                        <label class="toggle-switch theme-high-contrast">
                            <input type="checkbox">
                            <span class="toggle-switch-slider"></span>
                        </label>
                        <span class="ml-3">Enable High Contrast Mode!</span>
                    </li>
                </ul>

                <hr>
                <h6>General Settings</h6>
                <ul class="setting-list list-unstyled">
                    <li>
                        <label class="fancy-checkbox">
                            <input type="checkbox" name="checkbox" checked>
                            <span>Allowed Notifications</span>
                        </label>
                    </li>
                    <li>
                        <label class="fancy-checkbox">
                            <input type="checkbox" name="checkbox">
                            <span>Offline</span>
                        </label>
                    </li>
                    <li>
                        <label class="fancy-checkbox">
                            <input type="checkbox" name="checkbox">
                            <span>Location Permission</span>
                        </label>
                    </li>
                </ul>
            </div>
            <div class="tab-pane" id="question">
                <form>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Search...">
                    </div>
                </form>
                <ul class="list-unstyled question">
                    <li class="menu-heading">HELP</li>
                    <li><a href="javascript:void(0);"><i class="fa fa-question-circle"></i> FAQs</a></li>
                    <li><a href="javascript:void(0);"><i class="fa fa-lock"></i> Privacy Policy</a></li>
                    <li><a href="javascript:void(0);"><i class="fa fa-file-text"></i> Terms & Conditions</a></li>
                    <li class="menu-button mt-3">
                        <a href="javascript:void(0);" class="btn btn-primary btn-block"><i class="fa fa-book"></i> Documentation</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

