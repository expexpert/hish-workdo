@extends('layouts.admin')
@section('page-title')
{{__('Customer Account Statement Summary')}}
@endsection
@push('script-page')
<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
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

    $(document).ready(function() {
        var filename = $('#filename').val();
        $('#report-dataTable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                    extend: 'excel',
                    title: filename
                },
                {
                    extend: 'pdf',
                    title: filename
                }, {
                    extend: 'csv',
                    title: filename
                }
            ]
        });
    });
</script>
@endpush
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Validation Center')}}</li>

<li class="breadcrumb-item">{{__('Account Statement')}}</li>
@endsection


@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class=" multi-collapse mt-2 " id="multiCollapseExample1">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['route' => ['customer.bank.statements'], 'method' => 'GET', 'id' => 'customer_submit']) }}
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
                            <a href="{{ route('customer.bank.statements') }}" class="btn btn-sm btn-danger"
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
                                <th>{{__('Attachment')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($bankStatements))
                            @foreach ($bankStatements as $bankStatement)
                            <tr class="font-style">
                                <td>
                                    {{ $bankStatement->month_year ? \Carbon\Carbon::createFromFormat('m-Y', $bankStatement->month_year)->format('M Y') : '-' }}
                                </td>
                                @if (\Auth::user()->type == 'company')
                                <td>{{ $bankStatement->customer->accountant->name ?? '-' }}</td>
                                @endif
                                <td><a href="{{ route('customer.show', \Crypt::encrypt($bankStatement->customer_id)) }}" target="_blank">{{ $bankStatement->customer->name ?? '-' }}</a></td>
                                <td>
                                    @if($bankStatement->file_path)
                                    <a href="{{ route('customer.bank-statements.view-file', $bankStatement->id) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-primary">
                                        {{ __('View Attachment') }}
                                    </a>
                                    @else
                                    {{ __('No Attachment') }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection