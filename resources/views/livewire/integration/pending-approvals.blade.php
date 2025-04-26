@extends('layouts.app')
@section('content')

    <div>
        <div class="block-header">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-sm-12">
                    <h2>Pending Approvals</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('integrations.index') }}">Integrations</a></li>
                        <li class="breadcrumb-item active">Pending Approvals</li>
                    </ul>
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
                                <select wire:model="type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="internal">Internal</option>
                                    <option value="external">External</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover m-b-0">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Department</th>
                                    <th>Awaiting</th>
                                    <th>Priority</th>
                                    <th>Submitted By</th>
                                    <th>Waiting Since</th>
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
                                                @case('submitted')
                                                    <span class="badge badge-warning">App Owner Approval</span>
                                                    @break
                                                @case('app_owner_approval')
                                                    <span class="badge badge-warning">IDI Approval</span>
                                                    @break
                                                @case('idi_approval')
                                                    <span class="badge badge-warning">Security Approval</span>
                                                    @break
                                                @case('security_approval')
                                                    <span class="badge badge-warning">Infrastructure Approval</span>
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
                                        <td>{{ $integration->createdBy->name ?? 'Unknown' }}</td>
                                        <td>
                                            @php
                                                // Get the current stage based on status
                                                $currentStage = '';
                                                switch($integration->status) {
                                                    case 'submitted':
                                                        $currentStage = 'request';
                                                        break;
                                                    case 'app_owner_approval':
                                                        $currentStage = 'app_owner';
                                                        break;
                                                    case 'idi_approval':
                                                        $currentStage = 'idi';
                                                        break;
                                                    case 'security_approval':
                                                        $currentStage = 'security';
                                                        break;
                                                    case 'infrastructure_approval':
                                                        $currentStage = 'infrastructure';
                                                        break;
                                                }

                                                // Get the date when the current approval stage began
                                                $lastStageChange = $integration->approvalHistories()
                                                    ->where('stage', '!=', $currentStage)
                                                    ->orderBy('created_at', 'desc')
                                                    ->first();

                                                $waitingSince = $lastStageChange ? $lastStageChange->created_at : $integration->created_at;
                                                $waitingDays = now()->diffInDays($waitingSince);
                                            @endphp

                                            <span class="{{ $waitingDays > 3 ? 'text-danger' : '' }}">
                                                {{ $waitingSince->format('M d, Y') }}
                                                @if($waitingDays > 0)
                                                    ({{ $waitingDays }} days)
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('integrations.show', $integration) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No integration requests pending your approval.</td>
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
