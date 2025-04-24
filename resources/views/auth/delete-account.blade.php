@extends('layouts.app')

@section('content')
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h2>Delete Account</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profile</a></li>
                    <li class="breadcrumb-item active">Delete Account</li>
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
                    <h2>Delete Your Account</h2>
                </div>
                <div class="body">
                    <div class="alert alert-danger">
                        <h4 class="alert-heading"><i class="fa fa-exclamation-triangle"></i> Warning!</h4>
                        <p>This action is <strong>permanent</strong> and cannot be undone. Once your account is deleted, all of your data will be permanently removed.</p>
                    </div>

                    <p>Before proceeding, please consider the following:</p>
                    <ul>
                        <li>All your personal information will be deleted</li>
                        <li>All your data and history will be permanently removed</li>
                        <li>You will not be able to recover your account later</li>
                    </ul>

                    <p>If you are sure you want to delete your account, please enter your password below to confirm:</p>

                    <form action="{{ route('account.destroy') }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')

                        @if ($errors->any())
                            <div class="alert alert-warning">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your current password" required>
                            <small class="form-text text-muted">We need your password to confirm this action.</small>
                        </div>

                        <div class="form-group mt-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirmDelete" name="confirm_delete" required>
                                <label class="custom-control-label" for="confirmDelete">
                                    I understand that this action cannot be undone
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i> Permanently Delete Account
                            </button>
                            <a href="{{ route('profile.show') }}" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
