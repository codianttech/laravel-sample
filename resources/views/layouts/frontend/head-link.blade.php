<!-- Required meta tags -->
<meta charset="utf-8">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="csrf-token" content="{{ csrf_token() }}" />

<!-- favicon -->
<link rel="apple-touch-icon" sizes="180x180" href="{{asset_path('assets/images/logo-small.png')}}">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">

<link rel="stylesheet"  href="{{ asset_path('assets/css/frontend/frontend.css') }}" type="text/css">


{!! returnScriptWithNonce(asset_path('assets/js/frontend/app.js')) !!}
{!! returnScriptWithNonce(asset_path('assets/js/frontend/complied-app.js')) !!}
