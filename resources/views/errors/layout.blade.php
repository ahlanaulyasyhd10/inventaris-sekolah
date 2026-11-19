<!doctype html>
<html lang="id" data-bs-theme="light" data-bs-theme-primary="red" data-bs-theme-radius="1">
<head>
    @include('partials.head')
    <title>@yield('code') - {{ config('app.name') }}</title>
</head>
<body class="border-top-wide border-primary d-flex flex-column">
<div class="page page-center">
    <div class="container-tight py-4">
        <div class="empty">
            <div class="empty-header">@yield('code')</div>
            <p class="empty-title">@yield('heading')</p>
            <p class="empty-subtitle text-secondary">@yield('message')</p>
            <div class="empty-action">
                <a href="{{ url('/') }}" class="btn btn-primary"><i class="ti ti-arrow-left me-1"></i> Kembali ke beranda</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
