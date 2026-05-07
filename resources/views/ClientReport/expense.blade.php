@extends('layouts.admin')
@section('page-title')
{{__('Customer Expense Summary')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Validation Center')}}</li>
<li class="breadcrumb-item">{{__('Expense')}}</li>
@endsection
@push('css-page')
<link rel="stylesheet" href="{{ asset('css/datatable/buttons.dataTables.min.css') }}">
@endpush

@push('script-page')
{{-- <script src="{{ asset('assets/js/plugins/simple-datatables.js') }}"></script>--}}
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script src="{{ asset('js/datatable/jszip.min.js') }}"></script>
<script src="{{ asset('js/datatable/pdfmake.min.js') }}"></script>
<script src="{{ asset('js/datatable/vfs_fonts.js') }}"></script>
{{-- <script src="{{ asset('js/datatable/dataTables.buttons.min.js') }}"></script>--}}
{{-- <script src="{{ asset('js/datatable/buttons.html5.min.js') }}"></script>--}}
{{-- <script type="text/javascript" src="{{ asset('js/datatable/buttons.print.min.js') }}"></script>--}}

<script>
    var filename = $('#filename').val();

    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: filename,
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 4,
                dpi: 72,
                letterRendering: true
            },
            jsPDF: {
                unit: 'in',
                format: 'A4'
            }
        };
        html2pdf().set(opt).from(element).save();

    }
</script>
@endpush


@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['customer.expenses'], 'method' => 'GET', 'id' => 'customer_submit']) }}
                    <div class="row d-flex align-items-center justify-content-end">
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                            <div class="btn-box">
                                {{ Form::label('start_date', __('Start Date'), ['class' => 'text-type']) }}
                                {{ Form::date('start_date', isset($_GET['start_date']) ? $_GET['start_date'] : '', ['class' => 'form-control', 'placeholder' => __('YYYY-MM-DD')]) }}
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                            <div class="btn-box">
                                {{ Form::label('end_date', __('End Date'), ['class' => 'text-type']) }}
                                {{ Form::date('end_date', isset($_GET['end_date']) ? $_GET['end_date'] : '', ['class' => 'form-control', 'placeholder' => __('YYYY-MM-DD')]) }}
                            </div>
                        </div>
                        @if (!\Auth::guard('customer')->check())
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 mr-2">
                            <div class="btn-box">
                                {{ Form::label('customer', __('Customer'), ['class' => 'text-type']) }}
                                {{ Form::select('customer', $customer, isset($_GET['customer']) ? $_GET['customer'] : '', ['class' => 'form-control']) }}
                            </div>
                        </div>
                        @endif
                        <div class="col-auto d-flex mt-4">

                            <a href="#" class="btn btn-sm btn-primary me-2"
                                onclick="document.getElementById('customer_submit').submit(); return false;"
                                data-bs-toggle="tooltip" title="{{ __('Search') }}"
                                data-original-title="{{ __('Apply') }}">
                                <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                            </a>
                            <a href="{{ route('customer.expenses') }}" class="btn btn-sm btn-danger"
                                data-bs-toggle="tooltip" title="{{ __('Reset') }}">
                                <span class="btn-inner--icon"><i class="ti ti-refresh text-white-off"></i></span>
                            </a>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>{{__('Date')}}</th>
                                @if (\Auth::user()->type == 'company')
                                <th>{{__('Accountant')}}</th>
                                @endif
                                <th>{{__('Customer')}}</th>
                                <th>{{__('Supplier')}}</th>
                                <th>{{__('Category')}}</th>
                                <th>{{__('Payment Method')}}</th>
                                <th>{{__('Notes')}}</th>
                                <th>{{__('TTC')}}</th>
                                <th>{{__('TVA')}}</th>
                                <th>{{__('Total TTC')}}</th>
                                <th>{{__('Total TVA')}}</th>
                                <th>{{__('File')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($expenses as $expense)
                            <tr>
                                <td>{{ \Auth::user()->dateFormat($expense->date)}}</td>
                                @if (\Auth::user()->type == 'company')
                                <td>{{ $expense->customer?->accountant->name ?? '-' }}</td>
                                @endif
                                <td><a href="{{ route('customer.show', \Crypt::encrypt($expense->customer_id)) }}" target="_blank">{{ $expense->customer?->name ?? '-' }}</a></td>
                                <td>{{ $expense->supplier?->name ?? '-' }}</td>
                                <td>{{ $expense->category->name ?? '-' }}</td>
                                <td>{{ $expense->payment_method ?? '-' }}</td>
                                <td style="max-width: 250px; overflow-wrap: break-word; word-wrap: break-word; white-space: normal;">
                                    @if($expense->notes)
                                    <span title="{{ $expense->notes }}">
                                        {{ \Illuminate\Support\Str::limit($expense->notes, 50, '...') }}
                                    </span>
                                    <br>
                                    <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#noteModal-{{ $expense->id }}">
                                        {{ __('View full note') }}
                                    </button>
                                    @else
                                    {{ __('-') }}
                                    @endif
                                </td>
                                <td>{{ \Auth::user()->priceFormat($expense->ttc) }}</td>
                                <td>{{ \Auth::user()->priceFormat($expense->tva) }}</td>
                                <td>{{ \Auth::user()->priceFormat($expense->total_ttc) }}</td>
                                <td>{{ \Auth::user()->priceFormat($expense->total_tva) }}</td>
                                <td>
                                    @if($expense->file)
                                    <a href="{{ route('customer.expenses.view-file', $expense->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @else
                                    {{ __('-') }}
                                    @endif
                                </td>
                                <td>
                                    <select class="form-select form-select-sm fw-bold border-2 transition w-100 expense-action {{ \App\Models\CustomerExpense::getExpenseActionStyles($expense->review_status) }}"
                                        data-id="{{ $expense->id }}" style="min-width:80px">

                                        <option value="" disabled {{ $expense->review_status == 'PENDING' ? 'selected' : '' }}>
                                            {{ __('Pending') }}
                                        </option>

                                        <option value="VALIDATED"
                                            {{ $expense->review_status == 'VALIDATED' ? 'selected' : '' }}>
                                            {{ __('Validate') }}
                                        </option>

                                        <option value="REJECTED"
                                            {{ $expense->review_status == 'REJECTED' ? 'selected' : '' }}>
                                            {{ __('Reject') }}
                                        </option>

                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @foreach ($expenses as $expense)
                    <div class="modal fade" id="noteModal-{{ $expense->id }}" tabindex="-1" aria-labelledby="noteModalLabel-{{ $expense->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="noteModalLabel-{{ $expense->id }}">{{ __('Expense Notes') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $expense->notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('change', function(e) {

        if (e.target.classList.contains('expense-action')) {

            let action = e.target.value;
            let expenseId = e.target.dataset.id;

            if (!confirm('Are you sure you want to perform this action?')) {
                return;
            }

            fetch("{{ route('expense.review.action') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        expense_id: expenseId,
                        action: action
                    })
                })
                .then(res => res.json())
                .then(data => {

                    if (data.success) {
                        console.log("Status updated");
                        show_toastr('success', '{{ __('Action performed successfully') }}');
                        const styleMap = {
                            'VALIDATED': 'bg-light text-success border-success',
                            'EDIT_REQUESTED': 'bg-light text-warning border-warning',
                            'REJECTED': 'bg-light text-danger border-danger',
                            '': 'bg-white text-muted border-secondary'
                        };
                        e.target.className = `form-select form-select-sm fw-bold border-2 transition w-100 expense-action ${styleMap[action]}`;

                    } else {
                        alert("Something went wrong");
                        show_toastr('error', '{{ __('Failed to perform action') }}');
                    }
                })
                .catch(err => console.error(err));
        }
    });
</script>
@endsection