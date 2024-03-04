<!doctype html>
<html>
    <head>
        @include('head')
        @stack('styles')
        @stack('jsHeader')
    </head>
    <body>
        <header>@include('header')</header>
        @yield('content')
        <footer>@include('footer')</footer>
        @stack('jsFooter')
    </body>
</html>
