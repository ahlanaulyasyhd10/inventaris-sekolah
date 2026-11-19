<!doctype html>
<html lang="id" data-bs-theme="light" data-bs-navbar-theme="dark" data-bs-theme-primary="red" data-bs-theme-radius="1">
<head>
    @include('partials.head')
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <script>(function () { try { var t = localStorage.getItem('inventa-theme'); if (t) { document.documentElement.setAttribute('data-bs-theme', t); } } catch (e) {} })();</script>
    @stack('styles')
    @livewireStyles
</head>
<body>
@php
    $authUser = auth()->user();
    $isAdmin = $authUser->isAdmin();

    // Struktur menu: [label, ikon, pola route aktif, route | [anak: [label, route, pola]]]
    if ($isAdmin) {
        $menu = [
            ['Dashboard', 'ti-layout-dashboard', 'dashboard', 'dashboard'],
            ['Data Master', 'ti-database', 'admin.items.*|admin.categories.*', [
                ['Barang', 'admin.items.index', 'admin.items.*'],
                ['Kategori', 'admin.categories.index', 'admin.categories.*'],
            ]],
            ['Transaksi', 'ti-arrows-exchange', 'admin.loans.*|admin.return-logs.*', [
                ['Peminjaman', 'admin.loans.index', 'admin.loans.*'],
                ['Pengembalian', 'admin.return-logs.index', 'admin.return-logs.*'],
            ]],
            ['Manajemen User', 'ti-users', 'admin.users.*', 'admin.users.index'],
            ['Activity Log', 'ti-history', 'admin.activity-logs.*', 'admin.activity-logs.index'],
            ['Pengaturan', 'ti-settings', 'admin.settings.*', 'admin.settings.index'],
        ];
    } else {
        $menu = [
            ['Dashboard', 'ti-layout-dashboard', 'dashboard', 'dashboard'],
            ['Daftar Barang', 'ti-box', 'catalog.*', 'catalog.index'],
            ['Peminjaman Saya', 'ti-arrows-exchange', 'my-loans.*', 'my-loans.index'],
        ];
    }
    $isActive = fn ($patterns) => request()->routeIs(...explode('|', $patterns));
@endphp
<div class="page">
    {{-- Navbar atas (struktur mengikuti Tabler: layout horizontal) --}}
    <header class="navbar navbar-expand-md d-print-none">
        <div class="container-xl">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-brand navbar-brand-autodark pe-0 pe-md-3">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none" aria-label="{{ config('app.name') }}">
                    <span class="d-inline-flex align-items-center justify-content-center bg-white rounded me-2" style="width:2.2rem;height:2.2rem;padding:.2rem"><img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name') }}" style="max-height:100%;max-width:100%"></span>
                    <span class="d-flex flex-column lh-1">
                        <span class="fw-bold">{{ config('app.name') }}</span>
                        <span class="d-none d-sm-block mt-1" style="font-size:.68rem;font-weight:400;letter-spacing:.03em;color:rgba(255,255,255,.62)">Sistem Inventaris Sekolah</span>
                    </span>
                </a>
            </div>

            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item me-2">
                    <a href="#" class="nav-link px-0" id="theme-toggle" title="Ganti mode terang/gelap" aria-label="Ganti mode terang/gelap"><i class="ti ti-moon fs-2" id="theme-icon"></i></a>
                </div>
                <div class="d-none d-md-flex me-3">
                    {{-- Notifikasi --}}
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifikasi">
                            <i class="ti ti-bell fs-2"></i>
                            @if (count($notifications))
                                <span class="badge bg-red"></span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card" style="min-width: 22rem">
                            <div class="card">
                                <div class="card-header"><h3 class="card-title">Notifikasi</h3></div>
                                <div class="list-group list-group-flush list-group-hoverable">
                                    @forelse ($notifications as $n)
                                        <a href="{{ $n['url'] }}" class="list-group-item list-group-item-action">
                                            <div class="row align-items-center">
                                                <div class="col-auto"><span class="avatar avatar-sm {{ $n['color'] }} text-white"><i class="ti {{ $n['icon'] }}"></i></span></div>
                                                <div class="col text-truncate">
                                                    <span class="text-body d-block">{{ $n['title'] }}</span>
                                                    <div class="d-block text-secondary text-truncate mt-n1">{{ $n['desc'] }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="list-group-item text-center text-secondary py-4">Tidak ada notifikasi baru.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Menu pengguna --}}
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Menu pengguna">
                        @include('partials.avatar', ['user' => $authUser, 'size' => 'sm'])
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ $authUser->name }}</div>
                            <div class="mt-1 small text-secondary">{{ $isAdmin ? 'Administrator' : 'User' }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="ti ti-user dropdown-item-icon"></i> Profil Saya</a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item"><i class="ti ti-logout dropdown-item-icon"></i> Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Menu utama --}}
    <div class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar">
                <div class="container-xl">
                    <div class="row flex-column flex-md-row flex-fill align-items-center">
                        <div class="col">
                            <ul class="navbar-nav">
                                @foreach ($menu as [$label, $icon, $pattern, $target])
                                    @if (is_array($target))
                                        <li class="nav-item dropdown {{ $isActive($pattern) ? 'active' : '' }}">
                                            <a class="nav-link dropdown-toggle" href="#menu-{{ $loop->index }}" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti {{ $icon }}"></i></span>
                                                <span class="nav-link-title">{{ $label }}</span>
                                            </a>
                                            <div class="dropdown-menu">
                                                @foreach ($target as [$childLabel, $childRoute, $childPattern])
                                                    <a class="dropdown-item {{ $isActive($childPattern) ? 'active' : '' }}" href="{{ route($childRoute) }}">{{ $childLabel }}</a>
                                                @endforeach
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item {{ $isActive($pattern) ? 'active' : '' }}">
                                            <a class="nav-link" href="{{ route($target) }}">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti {{ $icon }}"></i></span>
                                                <span class="nav-link-title">{{ $label }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        <div class="col col-md-auto d-md-none">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('profile.edit') }}"><span class="nav-link-icon"><i class="ti ti-user"></i></span><span class="nav-link-title">Profil Saya</span></a>
                                </li>
                                <li class="nav-item">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="nav-link w-100 text-start"><span class="nav-link-icon"><i class="ti ti-logout"></i></span><span class="nav-link-title">Keluar</span></button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        @hasSection('breadcrumb')
                            @yield('breadcrumb')
                        @else
                            <div class="page-pretitle">Overview</div>
                        @endif
                        <h2 class="page-title">@yield('title', 'Dashboard')</h2>
                    </div>
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            @yield('actions')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                @include('partials.flash')
                @yield('content')
            </div>
        </div>

        <footer class="footer footer-transparent d-print-none">
            <div class="container-xl">
                <div class="text-center text-secondary">
                    &copy; {{ now()->year }} <strong>{{ config('app.name') }}</strong> &middot; Sistem Inventaris dan Peminjaman Barang Sekolah
                </div>
            </div>
        </footer>
    </div>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/tabler/js/tabler.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
@livewireScripts
</body>
</html>
