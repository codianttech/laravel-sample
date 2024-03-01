@extends('layouts.admin.app')

@section('title', $errorCode)
@section('content')
<div class="nk-content ">
    <div class="nk-block nk-block-middle wide-md mx-auto">
        <div class="nk-block-content nk-error-ld text-center">
            <img class="nk-error-gfx" src="{{asset_path('assets/images/error-404.svg')}}" alt="error">
            <!-- <img class="nk-error-gfx" src="{{asset_path('assets/images/error-504.svg')}}" alt="error"> -->
            <div class="wide-xs mx-auto">
                <!-- {{$errorCode}} -->
                <h3 class="nk-error-title">{{__('message.error.page.oops')}}</h3>
                <p class="nk-error-text">{{__('message.error.page.we_are_sorry')}}<br class="d-none d-sm-block">{{__('message.error.page.we_are_sorry_a_page')}}</p>
                <a href="{{route('frontend.home')}}" class="btn btn-lg btn-primary mt-2">{{__('message.error.page.back_to_home')}}</a>
            </div>
        </div>
    </div><!-- .nk-block -->
</div>
@endsection
