@extends('layouts.admin')
@section('page-title')
{{__('Customer Transaction Summary')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Customer Report')}}</li>
<li class="breadcrumb-item">{{__('Transaction')}}</li>
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
                                <th>{{__('Type')}}</th>
                                <th>{{__('Customer')}}</th>
                                <th>{{__('Account')}}</th>
                                <th>{{__('Category')}}</th>
                                <th>{{__('Description')}}</th>
                                <th>{{__('Amount')}}</th>
                                <th>{{__('Reference')}}</th>
                                <th>{{__('Payment Receipt')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ \Auth::user()->dateFormat($transaction->transaction_date)}}</td>
                                <td>{{ $transaction->type}}</td>
                                <td>{{ $transaction->customer?->name ?? '-' }}</td>
                                <td>@if(!empty($transaction->bankAccount()) && $transaction->bankAccount()->holder_name)
                                            {{$transaction->bankAccount()->holder_name}}
                                        @endif
                                </td>
                                <td>{{ $transaction->category->name ?? '-' }}</td>
                                <td>{{ !empty($transaction->description)?$transaction->description:'-'}}</td>
                                <td>{{\Auth::user()->priceFormat($transaction->amount)}}</td>
                                <td>{{ !empty($transaction->reference)?$transaction->reference:'-' }}</td>
                                <td>
                                    @if($transaction->attachment_path)
                                        <a href="{{ Storage::url($transaction->attachment_path) }}" target="_blank" class="btn btn-sm btn-primary">
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