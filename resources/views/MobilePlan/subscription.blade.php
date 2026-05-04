@extends('layouts.admin')
@section('page-title')
{{__('Mobile Subscriptions')}}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
<li class="breadcrumb-item">{{__('Mobile Subscriptions')}}</li>
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
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Mobile Plan') }}</th>
                                <th>{{ __('Mobile Plan Price') }}</th>
                                <th>{{ __('Referral Code') }}</th>
                                <th>{{ __('Billing Cycle') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Original Price') }}</th>
                                <th>{{ __('Referral Discount') }}</th>
                                <th>{{ __('Price Paid') }}</th>
                                <th>{{ __('Currency') }}</th>
                                <th>{{ __('Starts At') }}</th>
                                <th>{{ __('End At') }}</th>
                                <th>{{ __('Renew At') }}</th>
                                <th>{{ __('Trial End At') }}</th>
                                <th>{{ __('Refund Status') }}</th>
                                <th>{{ __('Payment Provider') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($mobileSubscriptions as $subscription)
                            <tr class="font-style">
                                <td>{{ optional($subscription->customer)->name ?? '-' }}</td>
                                <td>{{ optional($subscription->plan)->name ?? '-' }}</td>
                                <td>{{ $subscription->price?->price ?? '-' }}</td>
                                <td>{{ optional($subscription->referralCode)->code ?? '-' }}</td>
                                <td>{{ $subscription->billing_cycle ?? '-' }}</td>
                                <td>{{ $subscription->status ?? '-' }}</td>
                                <td>{{ $subscription->original_price ?? '-' }}</td>
                                <td>{{ $subscription->referral_discount_amount ?? '-' }}</td>
                                <td>{{ $subscription->price_paid ?? '-' }}</td>
                                <td>{{ $subscription->currency ?? '-' }}</td>
                                <td>{{ optional($subscription->starts_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ optional($subscription->ends_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ optional($subscription->renews_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ optional($subscription->trial_ends_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>{{ $subscription->refund_status ?? '-' }}</td>
                                <td>{{ $subscription->payment_provider ?? '-' }}</td>
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