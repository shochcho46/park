<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('frontend.layout.meta')
    @include('frontend.layout.css')
    @stack('custome-css')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Header include --}}
        @include('frontend.layout.header')
        {{-- End of header include --}}

            @yield('content')
         
        {{-- End of main content --}}

        {{-- Footer section --}}
        @include('frontend.layout.footer')
        {{-- End of footer section --}}
    </div>
    @include('frontend.layout.js')
</body>

    @stack('custome-js')
</html>
