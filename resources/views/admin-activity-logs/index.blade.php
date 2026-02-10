@extends('layouts.admin')
@section('page-title')
{{ __('Admin Activity Logs') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item">{{ __('Admin Activity Logs') }}</li>
@endsection

@section('action-btn')
<div class="d-flex">
    <a href="{{ route('admin-activity-logs.export') }}" class="btn btn-sm btn-secondary me-2" data-bs-toggle="tooltip" title="{{ __('Export CSV') }}">
        <i class="ti ti-download"></i>
        {{ __('Export') }}
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-xxl-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>{{ __('Super Admin Activity Logs') }}</h5>
                <p class="text-muted text-xs mb-0">{{ __('All changes made by super admins while logged into your account') }}</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Date & Time') }}</th>
                            <th>{{ __('Admin') }}</th>
                            <th>{{ __('Action') }}</th>
                            <th>{{ __('Module') }}</th>
                            <th>{{ __('IP Address') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <span class="badge bg-info">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                                </td>
                                <td>
                                    @if($log->admin)
                                        <div class="text-sm">
                                            <strong>{{ $log->admin->name }}</strong>
                                            <br/>
                                            <span class="text-muted">{{ $log->admin->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-danger">{{ __('Unknown') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $action = strtolower($log->action);
                                    @endphp
                                    @if(strpos($action, 'login') === 0)
                                        <span class="badge bg-success">{{ __('Login') }}</span>
                                    @elseif(strpos($action, 'logout') === 0)
                                        <span class="badge bg-warning">{{ __('Logout') }}</span>
                                    @elseif(strpos($action, 'create') === 0)
                                        <span class="badge bg-primary">{{ __('Created') }}</span>
                                    @elseif(strpos($action, 'update') === 0)
                                        <span class="badge bg-info">{{ __('Updated') }}</span>
                                    @elseif(strpos($action, 'delete') === 0)
                                        <span class="badge bg-danger">{{ __('Deleted') }}</span>
                                    @elseif(strpos($action, 'status_change') === 0)
                                        <span class="badge bg-secondary">{{ __('Status Changed') }}</span>
                                    @elseif(strpos($action, 'payment_received') === 0)
                                        <span class="badge bg-success">{{ __('Payment Recorded') }}</span>
                                    @elseif(strpos($action, 'payment_refund') === 0)
                                        <span class="badge bg-warning">{{ __('Refund Issued') }}</span>
                                    @elseif(strpos($action, 'invoice_sent') === 0)
                                        <span class="badge bg-info">{{ __('Invoice Sent') }}</span>
                                    @elseif(strpos($action, 'bulk_') === 0)
                                        <span class="badge bg-purple">{{ __('Bulk Action') }}</span>
                                    @elseif(strpos($action, 'export') === 0)
                                        <span class="badge bg-teal">{{ __('Export') }}</span>
                                    @elseif(strpos($action, 'import') === 0)
                                        <span class="badge bg-cyan">{{ __('Import') }}</span>
                                    @elseif(strpos($action, 'settings_change') === 0)
                                        <span class="badge bg-orange">{{ __('Settings Changed') }}</span>
                                    @elseif(strpos($action, 'permission') === 0)
                                        <span class="badge bg-pink">{{ __('Permission Changed') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->model)
                                        <span class="text-sm">
                                            <strong>{{ $log->model }}</strong>
                                            @if($log->model_id)
                                                <br/>
                                                <span class="text-muted">#{{ $log->model_id }}</span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <code class="text-xs">{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin-activity-logs.show', $log->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{ __('View Details') }}">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger delete-activity" data-log-id="{{ $log->id }}" data-bs-toggle="tooltip" title="{{ __('Delete') }}">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted">{{ __('No activity logs found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('script-page')
<script>
    $(document).ready(function() {
        $('.delete-activity').on('click', function(e) {
            e.preventDefault();
            let logId = $(this).data('log-id');
            
            if (confirm('{{ __("Are you sure you want to delete this activity log?") }}')) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                $.ajax({
                    url: '{{ route("admin-activity-logs.destroy", "") }}/' + logId,
                    method: 'DELETE',
                    success: function(response) {
                        location.reload();
                    },
                    error: function(error) {
                        alert('{{ __("Error deleting activity log") }}');
                    }
                });
            }
        });
    });
</script>
@endpush

@endsection
