@extends('layouts.admin')
@section('page-title')
{{ __('Manage Customer Category') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item">{{ __('Customer Category') }}</li>
@endsection



@section('action-btn')
<div class="float-end">
    <a href="#" data-url="{{ route('customer-category.create') }}" data-ajax-popup="true" data-bs-toggle="tooltip"
        title="{{ __('Create') }}" title="{{ __('Create') }}" data-title="{{ __('Create New Category') }}"
        class="btn btn-sm btn-primary">
        <i class="ti ti-plus"></i>
    </a>
</div>
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
                                <th> {{ __('Category') }}</th>
                                <th> {{ __('Description') }}</th>
                                <th> {{ __('Status') }}</th>
                                <th width="10%"> {{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                            <tr>
                                <td class="font-style">{{ $category->name }}</td>
                                <td class="font-style">
                                    {{ $category->description }}
                                </td>
                                <td>
                                    @if ($category->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td class="Action">
                                    <span>
                                        <div class="action-btn me-2">
                                            <a href="#" class="mx-3 btn btn-sm align-items-center bg-info"
                                                data-url="{{ route('customer-category.edit', $category->id) }}"
                                                data-ajax-popup="true"
                                                data-title="{{ __('Edit Customer Category') }}"
                                                data-bs-toggle="tooltip" title="{{ __('Edit') }}"
                                                data-original-title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil text-white"></i>
                                            </a>
                                        </div>
                                        <div class="action-btn">
                                            {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => ['customer-category.destroy', $category->id],
                                            'id' => 'delete-form-' . $category->id,
                                            ]) !!}
                                            <a href="#"
                                                class="mx-3 btn btn-sm align-items-center bs-pass-para bg-danger"
                                                data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                data-original-title="{{ __('Delete') }}"
                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="document.getElementById('delete-form-{{ $category->id }}').submit();">
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