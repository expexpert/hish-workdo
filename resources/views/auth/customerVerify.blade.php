@extends('layouts.auth')

@section('page-title')
    {{__('Verify Email')}}
@endsection
@push('css-page')
    <style>
        .btn-login {
            font-size: 12px;
            color: #fff;
            font-family: 'Montserrat-SemiBold';
            background: #0f5ef7;
            margin-top: 20px;
            padding: 10px 30px;
            width: 100%;
            border-radius: 10px;
            border: none;
        }
    </style>
@endpush
@section('content')
    <div class="login-contain">
        <div class="login-inner-contain">
            <div class="login-form">
                <div class="page-title">
                    <h6>{{ __('Reset Your Password') }}</h6>
                </div>
                <p>{{ __('You are receiving this email because we received a password reset request for your account.') }}</p>
                
                <p>{{ __('Please use the following One-Time Password (OTP) to reset your password:') }}</p>

                <div style="text-align: center; margin: 30px 0;">
                    <div style="display: inline-block; padding: 15px 30px; background-color: #f4f4f4; border: 2px dashed #333; font-size: 32px; font-weight: bold; letter-spacing: 10px; color: #000;">
                        {{ $token }}
                    </div>
                </div>

                <p class="text-muted" style="font-size: 0.9em;">
                    {{ __('This code is valid for 60 minutes. If you did not request a password reset, no further action is required.') }}
                </p>
                
                <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                
                <p style="font-size: 0.8em; color: #777;">
                    {{ __('Regards,') }}<br>
                    {{ config('app.name') }}
                </p>
            </div>
        </div>
    </div>
@endsection
