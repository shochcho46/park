<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.meta')
    @include('layouts.css')
    @stack('custome-css')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        {{-- Header include --}}
        @include('layouts.header')
        {{-- End of header include --}}

        {{-- Sidebar include --}}
        @if(Auth::guard('web')->check())
            @include('layouts.sidebar')
        @elseif(Auth::guard('admin')->check())

            {{-- Hello {{Auth::guard('user')->user()->name}} --}}
            @include('layouts.adminsidebar')
        @endif

        {{-- End of sidebar include --}}

        {{-- Main content --}}
        <main class="app-main">
            <!-- Begin::App Content Header -->
            @yield('content')
            <!-- End::App Content Header -->
        </main>

        {{-- End of main content --}}

        {{-- Footer section --}}
        @include('layouts.footer')
        {{-- End of footer section --}}
    </div>
    @include('layouts.js')
</body>

    @stack('custome-js')
</html>
