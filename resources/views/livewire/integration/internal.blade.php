@extends('layouts.app')

@section('content')
    <div class="block-header">
        <div class="row">
            <div class="col-lg-5 col-md-8 col-sm-12">
                <h2>Internal Integration Requests</h2>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fa fa-dashboard"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('integrations.index') }}">Integrations</a>
                    </li>
                    <li class="breadcrumb-item active">Internal Requests</li>
                </ul>
            </div>
            <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                <a href="" data-toggle="modal" data-target="#integrationTypeModal" class="btn btn-primary btn-round btn-simple float-right m-l-10">
                    Create New
                </a>
            </div>
        </div>
    </div>

    @livewire('integration.internal-integration-form')
@endsection
