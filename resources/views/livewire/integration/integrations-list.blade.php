@extends('layouts.app')
@section('content')

    <div>
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2>All Integration Requests</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                        <li class="breadcrumb-item active">Integrations</li>
                    </ul>
                </div>
                <div class="col-lg-7 col-md-4 col-sm-12 text-right">
                    <a href="{{ route('integrations.create') }}" class="btn btn-primary btn-round btn-simple float-right m-l-10">Create New</a>
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Search...">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select wire:model="status" class="form-control">
                                            <option value="">All Statuses</option>
                                            <option value="draft">Draft</option>
                                            <option value="submitted">Submitted</option>
                                            <option value="app_owner_approval">App Owner Approval</option>
                                            <option value="idi_approval">IDI Approval</option>
                                            <option value="security_approval">Security Approval</option>
                                            <option value="infrastructure_approval">Infrastructure Approval</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select wire:model="type" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="internal">Internal</option>
                                            <option value="external">External</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <select wire:model="priority" class="form-control">
                                            <option value="">All Priorities</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0 c_list">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($integrations as $integration)
                                    <tr>
                                        <td>{{ $integration->name }}</td>
                                        <td>
                                            <span class="badge {{ $integration->type === 'internal' ? 'badge-primary' : 'badge-info' }}">
                                                {{ ucfirst($integration->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $integration->department }}</td>
                                        <td>
                                            @switch($integration->status)
                                                @case('draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                    @break
                                                @case('submitted')
                                                    <span class="badge badge-info">Submitted</span>
                                                    @break
                                                @case('app_owner_approval')
                                                    <span class="badge badge-primary">App Owner Review</span>
                                                    @break
                                                @case('idi_approval')
                                                    <span class="badge badge-primary">IDI Review</span>
                                                    @break
                                                @case('security_approval')
                                                    <span class="badge badge-primary">Security Review</span>
                                                    @break
                                                @case('infrastructure_approval')
                                                    <span class="badge badge-primary">Infrastructure Review</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success">Approved</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-light">{{ $integration->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            @switch($integration->priority)
                                                @case('low')
                                                    <span class="badge badge-light">Low</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge badge-warning">Medium</span>
                                                    @break
                                                @case('high')
                                                    <span class="badge badge-danger">High</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-light">{{ $integration->priority }}</span>
                                            @endswitch
                                        </td>
                                        <td>{{ $integration->creator->name }}</td>
                                        <td>{{ $integration->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('integrations.show', $integration) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No integration requests found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $integrations->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
