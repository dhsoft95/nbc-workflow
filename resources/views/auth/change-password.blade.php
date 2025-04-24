@extends('layouts.app')

@section('content')
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Change Password</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profile</a></li>
                    <li class="breadcrumb-item active">Change Password</li>
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
                    <h2>Update Your Password</h2>
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

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                    <small class="form-text text-muted">Enter your current password to verify your identity.</small>
                                </div>

                                <div class="form-group">
                                    <label for="password">New Password</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    <small class="form-text text-muted">
                                        Your password must be at least 8 characters long.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                    <small class="form-text text-muted">Please confirm your new password.</small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-lock"></i> Update Password
                                    </button>
                                    <a href="{{ route('profile.show') }}" class="btn btn-default ml-2">Cancel</a>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="card bg-light">
                                    <div class="body">
                                        <h5><i class="fa fa-info-circle"></i> Password Tips</h5>
                                        <hr>
                                        <p>For a strong password, include:</p>
                                        <ul>
                                            <li>At least 8 characters</li>
                                            <li>Upper and lowercase letters</li>
                                            <li>Numbers</li>
                                            <li>Special characters (e.g., !@#$%^&*)</li>
                                        </ul>
                                        <p class="mb-0">Avoid using:</p>
                                        <ul>
                                            <li>Personal information (name, birthdate)</li>
                                            <li>Common words or phrases</li>
                                            <li>Sequential characters (123, abc)</li>
                                            <li>The same password used on other sites</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
