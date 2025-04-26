@extends('layouts.app')

@section('content')
    <div class="block-header">
        <div class="row">
            <div class="col-lg-6 col-md-8 col-sm-12">
                <h2 style="color: #152755">SLA Configuration Management</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a>
                    </li>
                    <li class="breadcrumb-item">Administration</li>
                    <li class="breadcrumb-item active">SLA</li>
                </ul>
            </div>
        </div>
    </div>
    @livewire('admin.sla-configuration-manager')
@endsection
