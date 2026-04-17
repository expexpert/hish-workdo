@extends('layouts.admin')
@section('page-title')
{{__('Customer Quote Summary')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Customer Report')}}</li>
<li class="breadcrumb-item">{{__('Quote')}}</li>
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
                    {{ Form::open(['route' => ['customer.quotes'], 'method' => 'GET', 'id' => 'customer_submit']) }}
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
                            <a href="{{ route('customer.quotes') }}" class="btn btn-sm btn-danger"
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
                                <th>{{__('Client')}}</th>
                                <th>{{__('Quote No')}}</th>
                                <th>{{__('Payment Via')}}</th>
                                <th>{{__('Notes')}}</th>
                                <th>{{__('Status')}}</th>
                                <th>{{__('TTC')}}</th>
                                <th>{{__('Articles')}}</th>
                                <th>{{__('Document')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($quotes as $quote)
                            <tr>
                                <td>{{ \Auth::user()->dateFormat($quote->date)}}</td>
                                @if (\Auth::user()->type == 'company')
                                <td>{{ $quote->customer?->accountant->name ?? '-' }}</td>
                                @endif
                                <td><a href="{{ route('customer.show', \Crypt::encrypt($quote->customer_id)) }}" target="_blank">{{ $quote->customer?->name ?? '-' }}</a></td>
                                <td>{{ $quote->client?->client_name ?? '-' }}</td>
                                <td>{{ $quote->quote_number ?? '-' }}</td>
                                <td>{{ $quote->payment_method ?? '-' }}</td>
                                <td style="max-width: 100px; overflow-wrap: break-word; word-wrap: break-word; white-space: normal;">
                                    @if($quote->notes)
                                    <span title="{{ $quote->notes }}">
                                        {{ \Illuminate\Support\Str::limit($quote->notes, 30, '...') }}
                                    </span>
                                    <br>
                                    <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#noteModal-{{ $quote->id }}">
                                        {{ __('View note') }}
                                    </button>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>{{ $quote->status ?? '-' }}</td>
                                @php
                                $totalTtc = 0;
                                if ($quote->articles && $quote->articles->count()) {
                                $totalTtc=$quote->articles->sum(function ($article) {
                                $ht = floatval($article->total_price_ht ?? 0);
                                $tvaPct = floatval($article->tax ? $article->tax->rate : 0) / 100;
                                return $ht + ($ht * $tvaPct);
                                });
                                }
                                @endphp
                                <td>{{ \Auth::user()->priceFormat($totalTtc) }}</td>
                                <td>
                                    @php $count = $quote->articles?->count() ?? 0; @endphp
                                    @if($count > 0)
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#articlesModal-{{ $quote->id }}">
                                        {{ __('View') }} ({{ $count }})
                                    </button>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if($quote->document_path)
                                    <a href="{{ route('customer.quotes.view-file', $quote->id) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @else
                                    {{ __('No Document') }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @foreach ($quotes as $quote)
                    @if(($quote->articles?->count() ?? 0) > 0)
                    <div class="modal fade" id="articlesModal-{{ $quote->id }}" tabindex="-1" aria-labelledby="articlesLabel-{{ $quote->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="articlesLabel-{{ $quote->id }}">{{ __('quote Articles') }} — {{ $quote->quote_number ?? '#' }}</h5>
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
                                                $grandTotalTVA = 0;
                                                @endphp
                                                @foreach ($quote->articles as $article)
                                                @php
                                                $lineHT = (float) $article->total_price_ht;
                                                $lineTvaPct = (float) ($article->tax ? $article->tax->rate : 0);
                                                $lineTva = $lineHT * ($lineTvaPct / 100);
                                                $grandTotalHT += $lineHT;
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
                                                $grandTotalTTC = $grandTotalHT + $grandTotalTVA;
                                                $avgTvaPct = $grandTotalHT > 0 ? ($grandTotalTVA / $grandTotalHT) * 100 : 0;
                                                @endphp
                                            <tfoot>
                                                <tr class="fw-bold">
                                                    <td colspan="4" class="text-end">{{ __('Total HT') }}</td>
                                                    <td class="text-end">{{ number_format($grandTotalHT, 2) }}</td>
                                                </tr>
                                                <tr class="fw-bold">
                                                    <td colspan="4" class="text-end">{{ __('TVA') }} ({{ number_format($avgTvaPct, 2) }}%)</td>
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


                    @foreach ($quotes as $quote)
                    @if ($quote->notes)
                    <div class="modal fade" id="noteModal-{{ $quote->id }}" tabindex="-1" aria-labelledby="noteLabel-{{ $quote->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="noteLabel-{{ $quote->id }}">{{ __('Full Note') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $quote->notes }}</p>
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