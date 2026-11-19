@extends('layouts.app')
@section('title', 'Dashboard')

@section('actions')
    <a href="{{ route('catalog.index') }}" class="btn d-none d-sm-inline-flex"><i class="ti ti-box me-1"></i> Daftar Barang</a>
    <a href="{{ route('my-loans.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Ajukan Peminjaman</a>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <div class="col-sm-12 col-lg-6">
            <div class="card card-gradient">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-12 col-sm-6 d-flex flex-column">
                            <h3 class="h2">Halo, {{ auth()->user()->name }}</h3>
                            <p class="text-secondary">
                                @if ($late > 0)
                                    Kamu punya <strong>{{ format_number($late) }}</strong> peminjaman yang terlambat. Segera kembalikan ya.
                                @elseif ($active > 0)
                                    Kamu sedang meminjam <strong>{{ format_number($active) }}</strong> barang. Pantau batas pengembaliannya di bawah.
                                @else
                                    Cari barang yang kamu butuhkan lalu ajukan peminjaman dengan mudah.
                                @endif
                            </p>
                            <div class="row g-5 mt-auto">
                                <div class="col-auto">
                                    <div class="subheader">Sudah Dikembalikan</div>
                                    <div class="h3">{{ format_number($returned) }}</div>
                                    <div class="progress progress-sm"><div class="progress-bar bg-success" style="width: {{ $total ? round($returned / $total * 100) : 0 }}%"></div></div>
                                </div>
                                <div class="col-auto">
                                    <div class="subheader">Terlambat</div>
                                    <div class="h3">{{ format_number($late) }}</div>
                                    <div class="progress progress-sm"><div class="progress-bar bg-danger" style="width: {{ $total ? round($late / $total * 100) : 0 }}%"></div></div>
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
        <div class="col-lg-6">
            <div class="row row-cards">
                <div class="col-sm-6">
                    <div class="card"><div class="card-body">
                        <div class="subheader">Sedang Dipinjam</div>
                        <div class="h1 mb-0">{{ format_number($active) }}</div>
                        <div class="text-secondary mt-2">Barang di tanganmu saat ini</div>
                    </div></div>
                </div>
                <div class="col-sm-6">
                    <div class="card"><div class="card-body">
                        <div class="subheader">Menunggu Persetujuan</div>
                        <div class="h1 mb-0">{{ format_number($pending) }}</div>
                        <div class="text-secondary mt-2">Diverifikasi oleh Admin</div>
                    </div></div>
                </div>
                <div class="col-sm-6">
                    <div class="card"><div class="card-body">
                        <div class="subheader">Total Riwayat</div>
                        <div class="h1 mb-0">{{ format_number($total) }}</div>
                        <div class="text-secondary mt-2">Seluruh pengajuanmu</div>
                    </div></div>
                </div>
                <div class="col-sm-6">
                    <div class="card"><div class="card-body">
                        <div class="subheader">Terlambat</div>
                        <div class="h1 mb-0 {{ $late ? 'text-danger' : '' }}">{{ format_number($late) }}</div>
                        <div class="text-secondary mt-2">{{ $late ? 'Segera kembalikan' : 'Semua tepat waktu' }}</div>
                    </div></div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Barang yang Sedang Saya Pinjam</h3>
                    <div class="card-actions"><a href="{{ route('my-loans.index') }}">Lihat semua</a></div>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead><tr><th>Barang</th><th>Jumlah</th><th>Batas Kembali</th><th>Sisa Waktu</th><th class="w-1"></th></tr></thead>
                        <tbody>
                        @forelse ($activeLoans as $loan)
                            @php($days = (int) today()->diffInDays($loan->due_date, false))
                            <tr>
                                <td>{{ $loan->item->name }}</td>
                                <td>{{ format_number($loan->quantity) }}</td>
                                <td>{{ format_date($loan->due_date) }}</td>
                                <td>
                                    @if ($days < 0)
                                        <span class="badge bg-red-lt">Terlambat {{ format_number(abs($days)) }} hari</span>
                                        <div class="text-danger small mt-1">Denda berjalan {{ format_rupiah($loan->accruedFine()) }}</div>
                                    @elseif ($days <= 2)
                                        <span class="badge bg-yellow-lt">{{ $days === 0 ? 'Hari ini' : format_number($days).' hari lagi' }}</span>
                                    @else
                                        <span class="badge bg-green-lt">{{ format_number($days) }} hari lagi</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    @if ($loan->canBeExtended())
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-extend data-action="{{ route('my-loans.extend', $loan) }}" data-item="{{ $loan->item->name }}" data-due="{{ $loan->due_date->toDateString() }}"><i class="ti ti-calendar-plus me-1"></i> Perpanjang</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">Kamu sedang tidak meminjam barang apa pun.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Peminjaman Terbaru Saya</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter">
                        <thead><tr><th>Barang</th><th>Jumlah</th><th>Tanggal Pinjam</th><th>Batas Kembali</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse ($recentLoans as $loan)
                            <tr>
                                <td>{{ $loan->item->name }}</td>
                                <td>{{ format_number($loan->quantity) }}</td>
                                <td>{{ format_date($loan->loan_date) }}</td>
                                <td>{{ format_date($loan->due_date) }}</td>
                                <td><span class="badge {{ $loan->statusBadge() }}">{{ ucfirst($loan->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">Kamu belum pernah meminjam barang.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('partials.extend-modal')
@endsection
