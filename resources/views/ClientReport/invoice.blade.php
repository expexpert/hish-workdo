@extends('layouts.admin')
@section('page-title')
{{__('Customer Expense Summary')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Customer Report')}}</li>
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
    <div class="col-md-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>{{__('Date')}}</th>
                                <th>{{__('Customer')}}</th>
                                <th>{{__('Client')}}</th>
                                <th>{{__('Invoice Number')}}</th>
                                <th>{{__('Payment Method')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('Articles')}}</th>
                                <th>{{__('Document')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($invoices as $invoice)
                            <tr>
                                <td>{{ \Auth::user()->dateFormat($invoice->date)}}</td>
                                <td>{{ $invoice->customer?->name ?? '-' }}</td>
                                <td>{{ $invoice->client?->client_name ?? '-' }}</td>
                                <td>{{ $invoice->invoice_number ?? '-' }}</td>
                                <td>{{ $invoice->payment_method ?? '-' }}</td>
                                <td>{{ $invoice->status ?? '-' }}</td>
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
                                    <a href="{{ Storage::url($invoice->document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                        {{ __('View Document') }}
                                    </a>
                                    @else
                                    {{ __('No Document') }}
                                    @endif
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
                                                        @foreach ($invoice->articles as $article)
                                                            <tr>
                                                                <td>{{ $article->designation }}</td>
                                                                <td class="text-end">{{ $article->quantity }}</td>
                                                                <td class="text-end">{{ number_format((float) $article->unit_price_ht, 2) }}</td>
                                                                <td class="text-end">{{ number_format((float) $article->tva_percentage, 2) }}</td>
                                                                <td class="text-end">{{ number_format((float) $article->total_price_ht, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
