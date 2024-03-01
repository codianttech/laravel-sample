@extends('layouts.admin.app')
@section('title','Forgot Password')
@section('content')

    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="javascript:void(0);" class="logo-link">
                                <x-logo logoClass="logo-img-lg"></x-logo>
                            </a>
                        </div>
                        <div class="card card-bordered border-0">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h5 class="nk-block-title ">Forgot Password</h5>
                                        <div class="nk-block-des ">
                                            <p>If you forgot your password, well, then we’ll email you instructions to Forgot your password.</p>
                                        </div>
                                    </div>
                                </div>
                                <form action="{{ route('admin.forgotPassword') }}" id="submitFrom" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="default-01">Email</label>
                                        </div>
                                        <input type="text" name="email" class="form-control form-control-lg" id="default-01" placeholder="Enter your email address">
                                    </div>
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-lg btn-primary btn-block">Send Reset Link</button>
                                    </div>
                                    {!! JsValidator::formRequest('App\Http\Requests\Admin\VerifyEmailRequest','#submitFrom') !!}
                                </form>
                                <div class="form-note-s2 text-center pt-4">
                                    <a href="{{route('admin.login')}}"><strong>Return to login</strong></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    @endsection
    <!-- app-root @e -->
    <!-- JavaScript -->


