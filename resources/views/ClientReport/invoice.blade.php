@extends('layouts.admin')
@section('page-title')
{{__('Customer Invoice Summary')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Validation Center')}}</li>
<li class="breadcrumb-item">{{__('Invoice')}}</li>
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
    <div class="col-xxl-12">
        <div class="row">
            <div class="col-lg-3 col-3 dashboard-card">
                <div class="card">
                    <div class="card-body">
                        <div class="theme-avtar bg-primary">
                            <i class="ti ti-file"></i>
                        </div>
                        <p class="text-muted text-sm mt-4 mb-2 ">{{ __('Total') }}</p>
                        <h6 class="mb-3 "><a href="{{ route('customer.invoices') }}" class="text-primary">{{__('Invoices')}}</a></h6>
                        <h3 class="mb-0 text-primary" id="total-invoices">{{ $data['totalInvoiceCount'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-3 dashboard-card">
                <div class="card">
                    <div class="card-body">
                        <div class="theme-avtar bg-warning">
                            <i class="ti ti-file"></i>
                        </div>
                        <p class="text-muted text-sm mt-4 mb-2 ">{{ __('Total') }}</p>
                        <h6 class="mb-3 "><a href="{{ route('customer.invoices', ['status' => 'pending']) }}" class="text-warning">{{__('Pending Invoices')}}</a></h6>
                        <h3 class="mb-0 text-warning" id="pending-invoices">{{ $data['totalPendingInvoiceCount'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-3 dashboard-card">
                <div class="card">
                    <div class="card-body">
                        <div class="theme-avtar bg-success">
                            <i class="ti ti-file"></i>
                        </div>
                        <p class="text-muted text-sm mt-4 mb-2 ">{{ __('Total') }}</p>
                        <h6 class="mb-3 "><a href="{{ route('customer.invoices', ['status' => 'validated']) }}" class="text-success">{{__('Validated Invoices')}}</a></h6>
                        <h3 class="mb-0 text-success" id="validated-invoices">{{ $data['totalApprovedInvoiceCount'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-3 dashboard-card">
                <div class="card">
                    <div class="card-body">
                        <div class="theme-avtar bg-danger">
                            <i class="ti ti-file"></i>
                        </div>
                        <p class="text-muted text-sm mt-4 mb-2 ">{{ __('Total') }}</p>
                        <h6 class="mb-3 "><a href="{{ route('customer.invoices', ['status' => 'rejected']) }}" class="text-danger">{{__('Rejected Invoices')}}</a></h6>
                        <h3 class="mb-0 text-danger" id="rejected-invoices">{{ $data['totalRejectedInvoiceCount'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['customer.invoices'], 'method' => 'GET', 'id' => 'customer_submit']) }}
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
                            <a href="{{ route('customer.invoices') }}" class="btn btn-sm btn-danger"
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
                                <th>{{__('Due Date')}}</th>
                                @if (\Auth::user()->type == 'company')
                                <th>{{__('Accountant')}}</th>
                                @endif
                                <th>{{__('Customer')}}</th>
                                <th>{{__('Client')}}</th>
                                <th>{{__('Invoice No')}}</th>
                                <th>{{__('Payment Via')}}</th>
                                <th>{{__('Notes')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('TTC')}}</th>
                                <th>{{__('Articles')}}</th>
                                <th>{{__('Document')}}</th>
                                <th>{{__('Actions')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($invoices as $invoice)
                            <tr>
                                <td>{{ \Auth::user()->dateFormat($invoice->date)}}</td>
                                <td>{{ \Auth::user()->dateFormat($invoice->due_date)}}</td>
                                @if (\Auth::user()->type == 'company')
                                <td>{{ $invoice->customer?->accountant->name ?? '-' }}</td>
                                @endif
                                <td><a href="{{ route('customer.show', \Crypt::encrypt($invoice->customer_id)) }}" target="_blank">{{ $invoice->customer?->name ?? '-' }}</a></td>
                                <td>{{ $invoice->client?->client_name ?? '-' }}</td>
                                <td>{{ $invoice->invoice_number ?? '-' }}</td>
                                <td>{{ $invoice->payment_method ?? '-' }}</td>
                                <td style="max-width: 100px; overflow-wrap: break-word; word-wrap: break-word; white-space: normal;">
                                    @if($invoice->notes)
                                    <span title="{{ $invoice->notes }}">
                                        {{ \Illuminate\Support\Str::limit($invoice->notes, 30, '...') }}
                                    </span>
                                    <br>
                                    <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#noteModal-{{ $invoice->id }}">
                                        {{ __('View note') }}
                                    </button>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>{{ $invoice->status ?? '-' }}</td>
                                @php
                                $totalTtc = 0;
                                if ($invoice->articles && $invoice->articles->count()) {
                                $totalTtc=$invoice->articles->sum(function ($article) {
                                $ht = floatval($article->total_price_ht ?? 0);
                                $afterDiscount = $ht - $article->discount;
                                $tvaPct = floatval($article->tax ? $article->tax->rate : 0) / 100;
                                return $afterDiscount + ($afterDiscount * $tvaPct);
                                });
                                }
                                @endphp
                                <td>{{ \Auth::user()->priceFormat($totalTtc) }}</td>
                                <td>
                                    @php $count = $invoice->articles?->count() ?? 0; @endphp
                                    @if($count > 0)
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#articlesModal-{{ $invoice->id }}">
                                        {{ __('View') }} ({{ $count }})
                                    </button>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if($invoice->document_path)
                                    <a href="{{ route('customer.invoices.view-file', $invoice->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @else
                                    {{ __('No Document') }}
                                    @endif
                                </td>
                                <td>
                                    <select class="form-select form-select-sm fw-bold border-2 transition w-100 invoice-action {{ \App\Models\CustomerInvoice::getInvoiceActionStyles($invoice->review_status) }}"
                                        data-id="{{ $invoice->id }}" style="min-width:80px">

                                        <option value="" disabled {{ $invoice->review_status == 'PENDING' ? 'selected' : '' }}>
                                            {{ __('Pending') }}
                                        </option>

                                        <option value="VALIDATED"
                                            {{ $invoice->review_status == 'VALIDATED' ? 'selected' : '' }}>
                                            {{ __('Validate') }}
                                        </option>
                                        <!-- 
                                        <option value="EDIT_REQUESTED"
                                            {{ $invoice->review_status == 'EDIT_REQUESTED' ? 'selected' : '' }}>
                                            {{ __('Edit') }}
                                        </option> -->

                                        <option value="REJECTED"
                                            {{ $invoice->review_status == 'REJECTED' ? 'selected' : '' }}>
                                            {{ __('Reject') }}
                                        </option>

                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @foreach ($invoices as $invoice)
                    @if(($invoice->articles?->count() ?? 0) > 0)
                    <div class="modal fade" id="articlesModal-{{ $invoice->id }}" tabindex="-1" aria-labelledby="articlesLabel-{{ $invoice->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="articlesLabel-{{ $invoice->id }}">{{ __('Invoice Articles') }} — {{ $invoice->invoice_number ?? '#' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Designation') }}</th>
                                                    <th class="text-end">{{ __('Qty') }}</th>
                                                    <th class="text-end">{{ __('Unit Price HT') }}</th>
                                                    <th class="text-end">{{ __('TVA %') }}</th>
                                                    <th class="text-end">{{ __('Total HT') }}</th>
                                                </tr>
                                            </thead>
                                           <tbody>
    @php
        $grandTotalHT = 0;
        $grandTotalDiscount = 0;
        $grandAfterDiscount = 0;
        $grandTotalTVA = 0;
    @endphp

    @foreach ($invoice->articles as $article)
        @php
            $lineHT = (float) $article->total_price_ht;
            $discount = (float) $article->discount;

            // Step 1: After discount
            $afterDiscount = $lineHT - $discount;

            // Step 2: VAT on discounted value
            $lineTvaPct = (float) ($article->tax ? $article->tax->rate : 0);
            $lineTva = $afterDiscount * ($lineTvaPct / 100);

            // Totals
            $grandTotalHT += $lineHT;
            $grandTotalDiscount += $discount;
            $grandAfterDiscount += $afterDiscount;
            $grandTotalTVA += $lineTva;
        @endphp

        <tr>
            <td>{{ $article->designation }}</td>
            <td class="text-end">{{ $article->quantity }}</td>
            <td class="text-end">{{ number_format((float) $article->unit_price_ht, 2) }}</td>
            <td class="text-end">{{ number_format($lineTvaPct, 2) }}</td>
            <td class="text-end">{{ number_format($lineHT, 2) }}</td>
        </tr>
    @endforeach

    @php
        // ✅ Correct TTC
        $grandTotalTTC = $grandAfterDiscount + $grandTotalTVA;

        // ✅ Correct Average VAT (based on AFTER DISCOUNT, not HT)
        $avgTvaPct = $grandAfterDiscount > 0
            ? ($grandTotalTVA / $grandAfterDiscount) * 100
            : 0;
    @endphp
</tbody> 
                                            <tfoot>
    <tr class="fw-bold">
        <td colspan="4" class="text-end">{{ __('Total HT') }}</td>
        <td class="text-end">{{ number_format($grandTotalHT, 2) }}</td>
    </tr>

    <tr class="fw-bold">
        <td colspan="4" class="text-end">{{ __('Discount') }}</td>
        <td class="text-end">-{{ number_format($grandTotalDiscount, 2) }}</td>
    </tr>

    <tr class="fw-bold">
        <td colspan="4" class="text-end">{{ __('Net HT (After Discount)') }}</td>
        <td class="text-end">{{ number_format($grandAfterDiscount, 2) }}</td>
    </tr>

    <tr class="fw-bold">
        <td colspan="4" class="text-end">
            {{ __('TVA') }} 
            ({{ number_format($avgTvaPct, 2) }}%)
        </td>
        <td class="text-end">{{ number_format($grandTotalTVA, 2) }}</td>
    </tr>

    <tr class="fw-bold">
        <td colspan="4" class="text-end">{{ __('Total TTC') }}</td>
        <td class="text-end">{{ number_format($grandTotalTTC, 2) }}</td>
    </tr>
</tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach


                    @foreach ($invoices as $invoice)
                    @if ($invoice->notes)
                    <div class="modal fade" id="noteModal-{{ $invoice->id }}" tabindex="-1" aria-labelledby="noteLabel-{{ $invoice->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="noteLabel-{{ $invoice->id }}">{{ __('Full Note') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $invoice->notes }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('change', function(e) {

        if (e.target.classList.contains('invoice-action')) {

            let action = e.target.value;
            let invoiceId = e.target.dataset.id;

            if (!confirm('Are you sure you want to perform this action?')) {
                return;
            }

            fetch("{{ route('invoice.review.action') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        invoice_id: invoiceId,
                        action: action
                    })
                })
                .then(res => res.json())
                .then(data => {

                    if (data.success) {
                        console.log("Status updated");
                        show_toastr('success', '{{ __('Action performed successfully ') }}');
                        const styleMap = {
                            'VALIDATED': 'bg-light text-success border-success',
                            'EDIT_REQUESTED': 'bg-light text-warning border-warning',
                            'REJECTED': 'bg-light text-danger border-danger',
                            '': 'bg-white text-muted border-secondary'
                        };
                        e.target.className = `form-select form-select-sm fw-bold border-2 transition w-100 invoice-action ${styleMap[action]}`;

                        // Update counts
                        document.getElementById('total-invoices').textContent = data.counts.total;
                        document.getElementById('pending-invoices').textContent = data.counts.pending;
                        document.getElementById('validated-invoices').textContent = data.counts.validated;
                        document.getElementById('rejected-invoices').textContent = data.counts.rejected;

                    } else {
                        alert("Something went wrong");
                        show_toastr('error', '{{ __('Failed to perform action ') }}');
                    }
                })
                .catch(err => console.error(err));
        }
    });
</script>
@endsection