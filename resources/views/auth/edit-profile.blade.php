@extends('layouts.app')

@section('content')
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Edit Profile</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profile</a></li>
                    <li class="breadcrumb-item active">Edit Profile</li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="d-flex flex-row-reverse">
                    <div class="page_action">
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back to Profile</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row clearfix">
        <div class="col-lg-12">
            <div class="card">
                <div class="header">
                    <h2>Edit Your Profile</h2>
                </div>
                <div class="body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="department">Department</label>
                                    <input type="text" name="department" id="department" class="form-control" value="{{ old('department', $user->department ?? '') }}">
                                    <small class="form-text text-muted">Optional: Your department or team</small>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="card bg-light">
                                    <div class="body">
                                        <h5><i class="fa fa-info-circle"></i> Profile Information</h5>
                                        <hr>
                                        <p>You can update your basic profile information here.</p>
                                        <ul>
                                            <li>Your name will be displayed throughout the system</li>
                                            <li>Your email address is used for login and notifications</li>
                                            <li>Department information helps organize users in the system</li>
                                        </ul>
                                        <p>If you need to change your password, please use the <a href="{{ route('password.change') }}">Change Password</a> page.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row clearfix">
                            <div class="col-lg-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Changes
                                </button>
                                <a href="{{ route('profile.show') }}" class="btn btn-default ml-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
