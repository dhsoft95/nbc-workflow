@extends('layouts.app')
@section('title', 'Role & Permission Management')

@section('content')
    <!-- Page title -->
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <h2>{{ __('Role & Permission Management') }}</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a>
                    </li>
                    <li class="breadcrumb-item">Administration</li>
                    <li class="breadcrumb-item active">Role & Permissions</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                @livewire('role-permission-manager')
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (params) => {
                const data = Array.isArray(params) ? params[0] : params;

                const context = data.type || 'info';
                const message = data.message || 'Operation completed';
                const position = data.position || 'top-right';

                toastr.remove(); // Clear existing toasts
                toastr[context](message, '', {
                    closeButton: true,
                    timeOut: 5000,
                    positionClass: 'toast-' + position,
                    progressBar: true
                });
            });
        });
    </script>
@endsection
