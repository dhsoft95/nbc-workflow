@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2>My Integration Requests</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('integrations.index') }}">Integrations</a></li>
                        <li class="breadcrumb-item active">My Requests</li>
                    </ul>
                </div>
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <button type="button" class="btn btn-primary btn-round btn-simple float-right m-l-10" data-toggle="modal" data-target="#integrationTypeModal">
                        Create New
                    </button>
                </div>
            </div>
        </div>

        <!-- Integration requests list would go here -->
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

@endsection
