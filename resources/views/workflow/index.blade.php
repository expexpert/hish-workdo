@extends('layouts.admin')

@section('page-title')
    {{ __('Client Monthly Progress') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Workflow') }}</li>
@endsection

@section('action-btn')
    <form action="{{ route('workflow.index') }}" method="GET" class="d-inline-block">
        <div class="input-group input-group-sm">
            <span class="input-group-text fw-bold">{{ __('Year') }}</span>
            <select name="year" onchange="this.form.submit()" class="form-select form-select-sm">
                @for($y = date('Y'); $y >= 2025; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>
@endsection

@section('content')
    <div class="row">
        <div class="col-xxl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive" style="overflow-x:auto">
                        <table class="table table-bordered align-middle text-nowrap datatable" style="min-width:1600px">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th class="text-start" style="min-width:200px">{{ __('Customer Name') }}</th>
                                    @isset($accountantNames)
                                        <th class="text-start" style="min-width:180px">{{ __('Accountant') }}</th>
                                    @endisset
                                    @foreach(range(1, 12) as $m)
                                        <th style="width: 140px">{{ DateTime::createFromFormat('!m', $m)->format('M') }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customers as $customer)
                                <tr>
                                    <td class="fw-bold text-secondary">{{ $customer->name }}</td>
                                    @isset($accountantNames)
                                        <td class="text-secondary">{{ $accountantNames[$customer->created_by] ?? '-' }}</td>
                                    @endisset
                                    @foreach(range(1, 12) as $m)
                                        @php
                                            $row = $customer->monthStatuses->where('month', $m)->first();
                                            $status = $row?->status ?? 'MISSING_DOCUMENTS';
                                        @endphp
                                        <td class="p-1">
                                            <select 
                                                onchange="saveStatus(this, {{ $customer->id }}, {{ $m }}, {{ $year }})"
                                                class="form-select form-select-sm fw-bold border-2 transition w-100 {{ \App\Models\CustomerMonthStatus::getStatusStyles($status) }}"
                                                style="min-width:130px"
                                                @if(!isset($canUpdate) || !$canUpdate) disabled @endif
                                            >
                                                <option value="MISSING_DOCUMENTS" {{ $status == 'MISSING_DOCUMENTS' ? 'selected' : '' }}>{{ __('MISSING') }}</option>
                                                <option value="ON_TRACK" {{ $status == 'ON_TRACK' ? 'selected' : '' }}>{{ __('ON TRACK') }}</option>
                                                <option value="IN_REVIEW" {{ $status == 'IN_REVIEW' ? 'selected' : '' }}>{{ __('IN REVIEW') }}</option>
                                                <option value="CLOSED" {{ $status == 'CLOSED' ? 'selected' : '' }}>{{ __('CLOSED') }}</option>
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
function saveStatus(selectElement, customerId, month, year) {
    const status = selectElement.value;
    selectElement.style.opacity = '0.4';

    fetch("{{ route('workflow.update') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            customer_id: customerId,
            month: month,
            year: year,
            status: status
        })
    })
    .then(res => res.json())
    .then(data => {
        selectElement.style.opacity = '1';
        if (data && data.success) {
            show_toastr('success', '{{ __('Status updated successfully') }}');
        }
        
        const styleMap = {
            'ON_TRACK': 'bg-light text-success border-success',
            'MISSING_DOCUMENTS': 'bg-light text-danger border-danger',
            'IN_REVIEW': 'bg-light text-warning border-warning',
            'CLOSED': 'bg-light text-secondary border-secondary'
        };

        selectElement.className = `form-select form-select-sm fw-bold border-2 transition ${styleMap[status]}`;
    })
    .catch(() => {
        alert('Failed to save status.');
        selectElement.style.opacity = '1';
    });
}
</script>
@endpush
