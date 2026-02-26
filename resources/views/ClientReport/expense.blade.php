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
                                <th>{{__('Category')}}</th>
                                <th>{{__('Payment Method')}}</th>
                                <th>{{__('TTC')}}</th>
                                <th>{{__('TVA')}}</th>
                                <th>{{__('Total TTC')}}</th>
                                <th>{{__('Total TVA')}}</th>
                                <th>{{__('File')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($expenses as $expense)
                            <tr>
                                <td>{{ \Auth::user()->dateFormat($expense->date)}}</td>
                                <td>{{ $expense->customer?->name ?? '-' }}</td>
                                <td>{{ $expense->category->name ?? '-' }}</td>
                                <td>{{ $expense->payment_method ?? '-' }}</td>
                                <td>{{ \Auth::user()->priceFormat($expense->ttc) }}</td>
                                <td>{{ \Auth::user()->priceFormat($expense->tva) }}</td>
                                <td>{{ \Auth::user()->priceFormat($expense->total_ttc) }}</td>
                                <td>{{ \Auth::user()->priceFormat($expense->total_tva) }}</td>
                                <td>
                                    @if($expense->file)
                                    <a href="{{ Storage::url($expense->file) }}" target="_blank" class="btn btn-sm btn-primary">
                                        {{ __('View Receipt') }}
                                    </a>
                                    @else
                                    {{ __('No Receipt') }}
                                    @endif
                                </td>
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