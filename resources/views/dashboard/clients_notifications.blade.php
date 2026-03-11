@extends('layouts.admin')
@section('page-title')
{{ __('Accoutant Notification/Documents to clients') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('Notifications') }}</li>
@endsection



@section('content')
<div class="mt-4">
    <div class="col-xl-12">
        <div class="card">
            @php
            $hasDocument = $notifications->contains('data', 'document_notification');
            @endphp
            <div class="card-header card-body table-border-style">
                <h5></h5>
                <div class="table-responsive">
                    <table class="table datatable" id="pc-dt-simple">
                        <thead>
                            <tr>
                                @if (\Auth::user()->type == 'company')
                                <th scope="col">{{ __('From') }}</th>
                                @endif
                                <th scope="col">{{ __('To') }}</th>
                                <th scope="col">
                                    {{  __('Title / Document Type') }}
                                </th>
                                <th scope="col">{{ __('Notification / File') }}</th>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col" class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notifications as $notification)
                            <tr>
                                @if (\Auth::user()->type == 'company')
                                <td>{{ $notification->sender->name ?? '-' }}</td>
                                @endif
                                <td>{{ $notification->customer->name ?? '-' }}</td>

                                <td>{{ $notification->title ?? '-' }}</td>

                                <td>
                                    @if($notification->data == 'document_notification')
                                    {{-- Show the document path as a clickable link --}}
                                    <a href="{{ asset('storage/' . $notification->document) }}" target="_blank" class="text-primary">
                                        <i class="ti ti-file-description"></i> {{ __('View Document') }}
                                    </a>
                                    @else
                                    {{-- Show the regular message --}}
                                    {{ $notification->message ?? '-' }}
                                    @endif
                                </td>

                                <td>{{ \Carbon\Carbon::parse($notification->created_at)->translatedFormat('d M Y H:i') }}</td>
                                <td>
                                    <div class="text-end">
                                        <form action="{{ route('customer.notification.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger text-white">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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