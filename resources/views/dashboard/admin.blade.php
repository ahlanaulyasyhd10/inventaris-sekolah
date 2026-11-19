@extends('layouts.app')
@section('title', 'Dashboard')

@section('actions')
    <a href="{{ route('admin.loans.index') }}" class="btn d-none d-sm-inline-flex"><i class="ti ti-arrows-exchange me-1"></i> Peminjaman</a>
    <a href="{{ route('admin.loans.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Catat Peminjaman</a>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        {{-- Sambutan --}}
        <div class="col-sm-12 col-lg-6">
            <div class="card card-gradient">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-12 col-sm-6 d-flex flex-column">
                            <h3 class="h2">Selamat datang, {{ auth()->user()->name }}</h3>
                            <p class="text-secondary">
                                Ada <strong>{{ format_number($pendingCount) }}</strong> pengajuan menunggu persetujuan
                                dan <strong>{{ format_number($lowStockCount) }}</strong> barang yang stoknya menipis.
                            </p>
                            <div class="row g-5 mt-auto">
                                <div class="col-auto">
                                    <div class="subheader">Tingkat Pengembalian</div>
                                    <div class="h3">{{ $returnRate }}%</div>
                                    <div class="progress progress-sm"><div class="progress-bar bg-success" style="width: {{ $returnRate }}%"></div></div>
                                </div>
                                <div class="col-auto">
                                    <div class="subheader">Kondisi Baik</div>
                                    <div class="h3">{{ $goodConditionPct }}%</div>
                                    <div class="progress progress-sm"><div class="progress-bar bg-primary" style="width: {{ $goodConditionPct }}%"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 d-flex justify-content-center">
                            @include('partials.welcome-illustration')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">Total Peminjaman</div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ format_number(array_sum($chartData)) }}</div>
                        <div class="me-auto text-secondary small">6 bulan terakhir</div>
                    </div>
                    <div class="text-secondary mt-2">{{ format_number($returned) }} sudah dikembalikan</div>
                </div>
                <div id="chart-loan-spark" class="chart-sm"></div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">Ketersediaan Stok</div>
                    <div class="d-flex align-items-baseline mb-2">
                        <div class="h1 mb-0 me-2">{{ format_number($totalStock) }}</div>
                        <div class="me-auto text-secondary small">unit tersedia</div>
                    </div>
                    <div id="chart-availability" style="min-height: 170px"></div>
                </div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center"><div class="subheader">Total Barang</div></div>
                    <div class="h1 mb-3">{{ format_number($totalItems) }}</div>
                    <div class="d-flex mb-2"><div>Kondisi baik</div><div class="ms-auto">{{ $goodConditionPct }}%</div></div>
                    <div class="progress progress-sm"><div class="progress-bar bg-primary" style="width: {{ $goodConditionPct }}%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center"><div class="subheader">Sedang Dipinjam</div></div>
                    <div class="h1 mb-3">{{ format_number($borrowed) }}</div>
                    <div class="d-flex mb-2"><div>Menunggu persetujuan</div><div class="ms-auto">{{ format_number($pendingCount) }}</div></div>
                    <div class="progress progress-sm"><div class="progress-bar bg-azure" style="width: {{ min(100, $pendingCount * 10) }}%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center"><div class="subheader">Terlambat</div></div>
                    <div class="h1 mb-3 text-danger">{{ format_number($late) }}</div>
                    <div class="d-flex mb-2"><div>Perlu ditindaklanjuti</div><div class="ms-auto">{{ $late > 0 ? 'Segera' : 'Aman' }}</div></div>
                    <div class="progress progress-sm"><div class="progress-bar bg-danger" style="width: {{ min(100, $late * 20) }}%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center"><div class="subheader">Stok Menipis</div></div>
                    <div class="h1 mb-3 text-warning">{{ format_number($lowStockCount) }}</div>
                    <div class="d-flex mb-2"><div>Batas</div><div class="ms-auto">&le; {{ setting('low_stock_threshold') }} unit</div></div>
                    <div class="progress progress-sm"><div class="progress-bar bg-warning" style="width: {{ $totalItems ? min(100, round($lowStockCount / $totalItems * 100)) : 0 }}%"></div></div>
                </div>
            </div>
        </div>

        {{-- Kartu kecil --}}
        <div class="col-12">
            <div class="row row-cards">
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('admin.categories.index') }}" class="card card-sm card-link">
                        <div class="card-body"><div class="row align-items-center">
                            <div class="col-auto"><span class="bg-primary text-white avatar"><i class="ti ti-tags"></i></span></div>
                            <div class="col"><div class="fw-medium">{{ format_number($categoryCount) }} Kategori</div><div class="text-secondary">Data master</div></div>
                        </div></div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('admin.users.index') }}" class="card card-sm card-link">
                        <div class="card-body"><div class="row align-items-center">
                            <div class="col-auto"><span class="bg-green text-white avatar"><i class="ti ti-users"></i></span></div>
                            <div class="col"><div class="fw-medium">{{ format_number($userCount) }} Pengguna</div><div class="text-secondary">Guru &amp; siswa</div></div>
                        </div></div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('admin.return-logs.index') }}" class="card card-sm card-link">
                        <div class="card-body"><div class="row align-items-center">
                            <div class="col-auto"><span class="bg-azure text-white avatar"><i class="ti ti-arrow-back-up"></i></span></div>
                            <div class="col"><div class="fw-medium">{{ format_number($returned) }} Pengembalian</div><div class="text-secondary">Riwayat selesai</div></div>
                        </div></div>
                    </a>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('admin.return-logs.index', ['denda' => 'Belum dibayar']) }}" class="card card-sm card-link">
                        <div class="card-body"><div class="row align-items-center">
                            <div class="col-auto"><span class="bg-purple text-white avatar"><i class="ti ti-receipt-2"></i></span></div>
                            <div class="col">
                                <div class="fw-medium">{{ format_rupiah($unpaidFine) }}</div>
                                <div class="text-secondary">{{ $unpaidFine > 0 ? 'Denda belum dibayar' : 'Tidak ada denda tertunggak' }}</div>
                                @if ($runningFine > 0)
                                    <div class="text-danger small">+ {{ format_rupiah($runningFine) }} denda berjalan</div>
                                @endif
                            </div>
                        </div></div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Grafik --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Tren Peminjaman 6 Bulan Terakhir</h3>
                    <div id="chart-loan-trend" style="min-height: 290px"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Barang per Kategori</h3>
                    <div id="chart-category" style="min-height: 290px"></div>
                </div>
            </div>
        </div>

        {{-- Menunggu persetujuan & stok menipis --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Menunggu Persetujuan</h3>
                    <div class="card-actions"><a href="{{ route('admin.loans.index') }}">Lihat semua</a></div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead><tr><th>Peminjam</th><th>Barang</th><th>Tgl Pinjam</th><th class="w-1"></th></tr></thead>
                        <tbody>
                        @forelse ($pendingLoans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->item->name }} <span class="text-secondary">&times; {{ format_number($loan->quantity) }}</span></td>
                                <td>{{ format_date($loan->loan_date) }}</td>
                                <td class="text-nowrap">
                                    <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui"><i class="ti ti-check"></i></button>
                                    </form>
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-outline-secondary" title="Detail"><i class="ti ti-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">Tidak ada pengajuan baru.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-alert-triangle text-warning me-2"></i>Stok Menipis</h3>
                    <div class="card-actions"><a href="{{ route('admin.items.index') }}">Kelola barang</a></div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($lowStockItems as $item)
                        <a href="{{ route('admin.items.edit', $item) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <div>
                                <div>{{ $item->name }}</div>
                                <div class="text-secondary small">{{ $item->category->name }} &middot; <code>{{ $item->code }}</code></div>
                            </div>
                            <span class="badge {{ $item->stock === 0 ? 'bg-red' : 'bg-yellow' }} text-white ms-auto">{{ $item->stock === 0 ? 'Habis' : format_number($item->stock).' unit' }}</span>
                        </a>
                    @empty
                        <div class="list-group-item text-center text-secondary py-4">Semua stok aman.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Terlambat & barang populer --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="ti ti-clock-exclamation text-red me-2"></i>Peminjaman Terlambat</h3></div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead><tr><th>Peminjam</th><th>Barang</th><th>Batas Kembali</th><th>Denda Berjalan</th><th class="w-1">Aksi</th></tr></thead>
                        <tbody>
                        @forelse ($lateLoans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ $loan->item->name }}</td>
                                <td><span class="badge bg-red-lt">{{ format_date($loan->due_date) }}</span></td>
                                <td class="text-danger">{{ format_rupiah($loan->accruedFine()) }}<div class="text-secondary small">{{ format_number($loan->daysLate()) }} hari</div></td>
                                <td class="text-nowrap">
                                    @if (whatsapp_url($loan->user->phone))
                                        <a href="{{ whatsapp_url($loan->user->phone) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success" title="Hubungi lewat WhatsApp"><i class="ti ti-brand-whatsapp"></i></a>
                                    @else
                                        <a href="{{ route('admin.users.edit', $loan->user) }}" class="btn btn-sm btn-outline-secondary" title="Nomor WhatsApp belum ada, klik untuk melengkapi"><i class="ti ti-phone-plus"></i></a>
                                    @endif
                                    <a href="{{ route('admin.loans.return', $loan) }}" class="btn btn-sm btn-primary" title="Catat pengembalian"><i class="ti ti-arrow-back-up"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">Tidak ada peminjaman terlambat.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="ti ti-trophy text-yellow me-2"></i>Barang Paling Sering Dipinjam</h3></div>
                <table class="table card-table table-vcenter">
                    <thead><tr><th>Barang</th><th class="text-end">Dipinjam</th><th class="w-50"></th></tr></thead>
                    <tbody>
                    @php($maxLoans = max(1, $topItems->max('loans_count') ?? 1))
                    @forelse ($topItems as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="text-end text-secondary">{{ format_number($item->loans_count) }}x</td>
                            <td><div class="progress progress-xs"><div class="progress-bar bg-primary" style="width: {{ round($item->loans_count / $maxLoans * 100) }}%"></div></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-secondary py-4">Belum ada data peminjaman.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- User aktif & aktivitas --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="ti ti-users text-green me-2"></i>User Paling Aktif</h3></div>
                <div class="list-group list-group-flush">
                    @forelse ($topUsers as $u)
                        <div class="list-group-item d-flex align-items-center">
                            <span class="avatar avatar-sm bg-primary-lt me-3">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                            <span>{{ $u->name }}</span>
                            <span class="badge bg-green text-green-fg ms-auto">{{ format_number($u->loans_count) }}x</span>
                        </div>
                    @empty
                        <div class="list-group-item text-center text-secondary py-4">Belum ada data peminjaman.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Terbaru</h3>
                    <div class="card-actions"><a href="{{ route('admin.activity-logs.index') }}">Lihat semua</a></div>
                </div>
                <div class="card-body">
                    <div class="divide-y">
                        @forelse ($recentActivities as $log)
                            <div>
                                <div class="row">
                                    <div class="col-auto"><span class="avatar avatar-sm bg-primary-lt">{{ strtoupper(mb_substr($log->user->name ?? '?', 0, 1)) }}</span></div>
                                    <div class="col">
                                        <div class="text-truncate"><strong>{{ $log->user->name ?? 'Akun terhapus' }}</strong> &middot; {{ $log->description }}</div>
                                        <div class="text-secondary">{{ $log->created_at->locale('id')->diffForHumans() }}</div>
                                    </div>
                                    <div class="col-auto align-self-center"><span class="badge {{ $log->actionBadge() }}">{{ $log->actionLabel() }}</span></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-secondary">Belum ada aktivitas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        (function () {
            var css = function (n) { return getComputedStyle(document.documentElement).getPropertyValue(n).trim(); };
            var color = function (name, fallback) { var v = css('--tblr-' + name + '-rgb'); return v ? 'rgb(' + v + ')' : fallback; };
            var primary = color('primary', '#d63939');
            var green = color('green', '#2fb344');
            var muted = css('--tblr-border-color') || '#e6e7e9';
            var fore = css('--tblr-body-color') || '#495057';
            var labels = @json($chartLabels);
            var data = @json($chartData);

            // Sparkline kecil di kartu "Total Peminjaman"
            new ApexCharts(document.getElementById('chart-loan-spark'), {
                chart: { type: 'area', height: 64, sparkline: { enabled: true }, animations: { enabled: false }, fontFamily: 'inherit', foreColor: fore },
                series: [{ name: 'Peminjaman', data: data }],
                stroke: { width: 2, curve: 'smooth' },
                fill: { opacity: 0.16, type: 'solid' },
                colors: [primary],
                xaxis: { categories: labels },
                tooltip: { theme: 'light' }
            }).render();

            // Gauge ketersediaan stok
            new ApexCharts(document.getElementById('chart-availability'), {
                chart: { type: 'radialBar', height: 190, fontFamily: 'inherit', foreColor: fore, sparkline: { enabled: true } },
                series: [{{ $availabilityPct }}],
                labels: ['Tersedia'],
                colors: [primary],
                plotOptions: { radialBar: { hollow: { size: '62%' }, track: { background: muted }, dataLabels: {
                    name: { show: true, fontSize: '12px', offsetY: -8 },
                    value: { fontSize: '28px', fontWeight: 600, offsetY: 4, formatter: function (v) { return v + '%'; } }
                } } }
            }).render();

            // Tren peminjaman
            new ApexCharts(document.getElementById('chart-loan-trend'), {
                chart: { type: 'area', height: 290, toolbar: { show: false }, fontFamily: 'inherit', foreColor: fore, animations: { enabled: false } },
                series: [{ name: 'Jumlah peminjaman', data: data }],
                colors: [primary],
                stroke: { width: 3, curve: 'smooth' },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
                dataLabels: { enabled: false },
                markers: { size: 4 },
                grid: { borderColor: muted, strokeDashArray: 4 },
                xaxis: { categories: labels, axisBorder: { show: false } },
                yaxis: { min: 0, forceNiceScale: true, labels: { formatter: function (v) { return Math.round(v); } } }
            }).render();

            // Distribusi barang per kategori
            new ApexCharts(document.getElementById('chart-category'), {
                chart: { type: 'donut', height: 290, fontFamily: 'inherit', foreColor: fore },
                series: @json($categoryData->values()),
                labels: @json($categoryLabels->values()),
                colors: [primary, green, color('blue', '#4299e1'), color('yellow', '#f59f00'), color('purple', '#ae3ec9'), color('azure', '#4299e1'), color('orange', '#f76707'), color('teal', '#0ca678')],
                legend: { position: 'bottom', fontSize: '12px' },
                dataLabels: { enabled: false },
                stroke: { width: 0 },
                plotOptions: { pie: { donut: { size: '68%' } } }
            }).render();
        })();
    </script>
@endpush
