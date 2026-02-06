@extends('layouts.admin')
@section('page-title')
{{ __('Activity Log Details') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{ route('admin-activity-logs.index') }}">{{ __('Activity Logs') }}</a>
</li>
<li class="breadcrumb-item">{{ __('Details') }}</li>
@endsection

@section('action-btn')
<div class="d-flex">
    <a href="{{ route('admin-activity-logs.index') }}" class="btn btn-sm btn-secondary">
        <i class="ti ti-arrow-left"></i>
        {{ __('Back') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-8 offset-xxl-2">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Event Details') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('Date & Time') }}</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-info">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('Action') }}</label>
                            <p class="form-control-plaintext">
                                @switch($log->action)
                                    @case('login')
                                        <span class="badge bg-success">{{ __('Login') }}</span>
                                        @break
                                    @case('logout')
                                        <span class="badge bg-warning">{{ __('Logout') }}</span>
                                        @break
                                    @case('create')
                                        <span class="badge bg-primary">{{ __('Created') }}</span>
                                        @break
                                    @case('update')
                                        <span class="badge bg-info">{{ __('Updated') }}</span>
                                        @break
                                    @case('delete')
                                        <span class="badge bg-danger">{{ __('Deleted') }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('Admin') }}</label>
                            <p class="form-control-plaintext">
                                @if($log->admin)
                                    <strong>{{ $log->admin->name }}</strong><br/>
                                    <small class="text-muted">{{ $log->admin->email }}</small>
                                @else
                                    <span class="text-danger">{{ __('Unknown') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('IP Address') }}</label>
                            <p class="form-control-plaintext">
                                <code>{{ $log->ip_address }}</code>
                            </p>
                        </div>
                    </div>
                </div>

                @if($log->model)
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('Model') }}</label>
                            <p class="form-control-plaintext">
                                <strong>{{ $log->model }}</strong>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">{{ __('Model ID') }}</label>
                            <p class="form-control-plaintext">
                                {{ $log->model_id ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                @if($log->changes)
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">{{ __('Changes Made') }}</label>
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <pre class="mb-0"><code>{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($log->user_agent)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">{{ __('User Agent') }}</label>
                            <p class="form-control-plaintext text-xs">
                                <code>{{ $log->user_agent }}</code>
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin-activity-logs.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                <form method="POST" action="{{ route('admin-activity-logs.destroy', $log->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __("Are you sure?") }}')">
                        <i class="ti ti-trash"></i>
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
