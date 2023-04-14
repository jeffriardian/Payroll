<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">  
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'SMM-APP') }}</title>

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    {{-- Styles --}}
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

    {{-- Internal Style --}}
    @yield('css')
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('partials._top_menu')
        @include('partials._sidebar_left')

        <div class="content-wrapper">
            @include('partials._breadcrumb')
            {{-- B : Content --}}
            <section class="content">
                @yield('content')
            </section>
            {{--  E : Content --}}  
        </div>

        {{-- Footer --}}
        @include('partials._footer')

        {{-- Sidebar Right --}}
        @include('partials._sidebar_right')
        
    </div>

    @include('partials._app_js')
    @yield('script')
</body>
</html>