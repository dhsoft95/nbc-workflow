@extends('layouts.app')
@section('content')
    <div>
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
{{--                    <a href="{{ route('integrations.create') }}" class="btn btn-primary btn-round btn-simple float-right m-l-10">Create New</a>--}}
                </div>
            </div>
        </div>

        <div class="row clearfix">
            <div class="col-12">
                <div class="card">
                    <div class="body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Search...">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0 c_list">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Submitted Date</th>
                                    <th>Current Stage</th>
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
                                        <td>{{ $integration->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @switch($integration->status)
                                                @case('app_owner_approval')
                                                    App Owner
                                                    @break
                                                @case('idi_approval')
                                                    IDI Team
                                                    @break
                                                @case('security_approval')
                                                    Security Team
                                                    @break
                                                @case('infrastructure_approval')
                                                    Infrastructure Team
                                                    @break
                                                @case('approved')
                                                    Completed
                                                    @break
                                                @case('rejected')
                                                    Rejected
                                                    @break
                                                @default
                                                    {{ ucfirst($integration->status) }}
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="{{ route('integrations.show', $integration) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">You haven't created any integration requests yet.</td>
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
