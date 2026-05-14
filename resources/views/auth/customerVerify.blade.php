@extends('layouts.auth')

@section('page-title')
{{__('Vérifier votre adresse e-mail')}}
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
                <h6>{{ __('Réinitialisez votre mot de passe') }}</h6>
            </div>
            <p>{{ __('Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.') }}</p>

            <p>{{ __('Veuillez utiliser le mot de passe à usage unique (OTP) suivant pour réinitialiser votre mot de passe :') }}</p>

            <div style="text-align: center; margin: 30px 0;">
                <div style="display: inline-block; padding: 15px 30px; background-color: #f4f4f4; border: 2px dashed #333; font-size: 32px; font-weight: bold; letter-spacing: 10px; color: #000;">
                    {{ $token }}
                </div>
            </div>

            <p class="text-muted" style="font-size: 0.9em;">
                {{ __('Ce code est valide pendant 60 minutes. Si vous n\'avez pas demandé de réinitialisation de mot de passe, aucune action supplémentaire n\'est requise.') }}
            </p>

            <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">

            <p style="font-size: 0.8em; color: #777;">
                {{ __('Cordialement,') }}<br>
                {{ config('app.name') }}
            </p>
        </div>
    </div>
</div>
@endsection