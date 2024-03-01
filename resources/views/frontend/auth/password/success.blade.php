@extends('layouts.admin.app')
@section('title','Reset Password Email Sent')
@section('content')

<body class="authPage nk-body npc-default pg-auth bg-gradient">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="{{ route('admin.login') }}" class="logo-link">
                                <x-logo logoClass="logo-img-lg"></x-logo>
                            </a>
                        </div>
                        <div class="card border-0 shadow-none">
                            <div class="card-inner card-inner-lg">

                                <div class="nk-block-head pb-0">
                                    <div class="nk-block-head-content text-center">
                                        <h4 class="nk-block-title">Thank you for submitting form</h4>
                                        <div class="nk-block-des">
                                            <p class="mb-0">Password reset instructions will be sent to the registered
                                                Email</p>
                                            <p>Please check your mail</p>
                                        </div>
                                    </div>
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
