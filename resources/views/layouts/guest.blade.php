<!doctype html>
<html lang="id" data-bs-theme="light" data-bs-theme-primary="red" data-bs-theme-radius="1">
<head>
    @include('partials.head')
    <title>@yield('title') - {{ config('app.name') }}</title>
</head>
<body class="d-flex flex-column">
<div class="page d-flex flex-column" style="min-height:100vh">
    <div class="container container-tight py-4 my-auto">
        <div class="text-center mb-4">
            <a href="{{ url('/') }}" class="navbar-brand navbar-brand-autodark d-inline-flex flex-column align-items-center text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name') }}" style="height:72px">
                <span class="h1 m-0 mt-2">{{ config('app.name') }}</span>
            </a>
            <div class="text-secondary mt-2">Sistem Inventaris Sekolah</div>
        </div>
        <div class="card card-md shadow-sm">
            <div class="card-body">
                @include('partials.flash')
                @yield('content')
            </div>
        </div>
        @yield('below')
    </div>
</div>
<script src="{{ asset('vendor/tabler/js/tabler.min.js') }}"></script>
</body>
</html>
