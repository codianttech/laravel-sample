@extends('layouts.admin.app')
@section('title','Login')
@section('content')

<div class="nk-content ">
    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
        <div class="brand-logo pb-4 text-center">
            <a href="{{route('frontend.home')}}" class="logo-link">
                <img class="logo-dark logo-img logo-img-lg" src="{{asset_path('assets/images/logo-dark.png')}}"
                    srcset="{{asset_path('assets/images/logo-dark.png')}}" alt="{{getAppName()}}-logo">
            </a>
        </div>
        <div class="card">
            <div class="card-inner card-inner-lg">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h4 class="nk-block-title">Sign-In</h4>
                        <div class="nk-block-des">
                            <p></p>
                        </div>
                    </div>
                </div>
                <form action="{{route('admin.otp.verify')}}" method="post" id="admin-otp-login-form" onsubmit="return false;">
                    @csrf
                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label" for="otp">OTP</label>
                            <a id="resentBtn" class="link link-primary link-sm" data-href="{{$resendOtpUrl}}">Resent OTP</a>
                        </div>
                        <div class="form-control-wrap">
                        <input type="hidden" name="email" value="{{ $email }}" id="email" >
                            <input type="text" class="form-control form-control-lg" id="otp" name="otp" placeholder="Enter your OTP" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" id="signInBtn" class="btn btn-lg btn-primary btn-block">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
{!! JsValidator::formRequest('App\Http\Requests\Admin\VerifyOtpRequest','#admin-otp-login-form') !!}
{!! returnScriptWithNonce(asset_path('assets/js/admin/auth/otp.js')) !!}
@endpush