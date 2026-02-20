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
<li class="breadcrumb-item">{{__('Customer Report')}}</li>

<li class="breadcrumb-item">{{__('Account Statement')}}</li>
@endsection


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
                                <th>{{__('Attachment')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($bankStatements))
                            @foreach ($bankStatements as $bankStatement)
                            <tr class="font-style">
                                <td>{{ $bankStatement->month_year ?? '-' }}</td>
                                <td>{{ $bankStatement->customer->name ?? '-' }}</td>
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