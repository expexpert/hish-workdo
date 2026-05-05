@extends('layouts.admin')
@section('page-title')
{{__('Manage Referral Codes')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Referral Codes')}}</li>
@endsection


@section('action-btn')
<div class="float-end">
    <a href="#" data-url="{{ route('referral.codes.create') }}" data-ajax-popup="true" data-title="{{__('Create Referral Code')}}" data-bs-toggle="tooltip" title="{{__('Create')}}" class="btn btn-sm btn-primary">
        <i class="ti ti-plus"></i>
    </a>
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Owner Name') }}</th>
                                <th>{{ __('Owner Email') }}</th>
                                <th>{{ __('Discount (%)') }}</th>
                                <th>{{ __('Commission (%)') }}</th>
                                <th>{{ __('Clicks') }}</th>
                                <th>{{ __('Used') }}</th>
                                <th>{{ __('Max Uses') }}</th>
                                <th>{{ __('Start At') }}</th>
                                <th>{{ __('End At') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th width="10%">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($referralCodes as $code)
                            <tr class="font-style">
                                <td>{{ $code->code }}</td>
                                <td>{{ ucfirst($code->type) }}</td>
                                <td>{{ $code->owner_name }}</td>
                                <td>{{ $code->owner_email }}</td>
                                <td>{{ $code->discount_percentage ?? 0 }}</td>
                                <td>{{ $code->commission_percentage ?? 0 }}</td>
                                <td>{{ $code->clicks ?? 0 }}</td>
                                <td>{{ $code->used_count ?? 0 }}</td>
                                <td>{{ $code->max_uses ?? 0 }}</td>
                                <td>{{ $code->starts_at ?? '-' }}</td>
                                <td>{{ $code->ends_at ?? '-'}}</td>
                                <td>
                                    @if($code->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>

                                <td class="Action">
                                    <span>
                                        <div class="action-btn me-2">
                                            <a href="#"
                                                class="btn btn-sm bg-info"
                                                data-url="{{ route('referral.codes.edit', $code->id) }}"
                                                data-ajax-popup="true"
                                                data-title="{{ __('Edit Referral Code') }}"
                                                data-bs-toggle="tooltip"
                                                title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>

                                        <div class="action-btn">
                                            {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['referral.codes.destroy', $code->id],
                                            'id' => 'delete-form-' . $code->id
                                            ]) !!}
                                            <a href="#"
                                                class="btn btn-sm bg-danger bs-pass-para"
                                                data-bs-toggle="tooltip"
                                                title="{{ __('Delete') }}"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-form-{{ $code->id }}').submit();">
                                                <i class="ti ti-trash text-white"></i>
                                            </a>
                                            {!! Form::close() !!}
                                        </div>
                                    </span>
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