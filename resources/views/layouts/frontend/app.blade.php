<!DOCTYPE html>
<html lang="{{config('app.locale')}}" class="js">

<head>
    <title>  @yield('title') | {{getAppName()}} </title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="meta-keywords" content="@yield('meta-keywords')" />
    <meta name="meta-title" content="@yield('meta-title')" />
    <meta name="meta-description" content="@yield('meta-description')" />
    @include('layouts.frontend.head-link')
    @stack('css')
</head>

<body class="nk-body bg-lighter npc-general pg-auth @yield('main-class')">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                @yield('content')
                <!-- content @e -->
            </div>
            <!-- footer @s -->
            @include('layouts.frontend.footer')
            <!-- footer @e -->
            <!-- main @e -->
        </div>
        @stack('scripts')
    </div>
</body>

</html>
