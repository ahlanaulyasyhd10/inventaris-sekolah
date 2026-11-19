@extends('layouts.app')
@section('title', 'Detail Barang')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Barang' => route('admin.items.index'), $item->name => null]])
@endsection
@section('actions')
    <button type="button" class="btn" onclick="window.print()"><i class="ti ti-printer me-1"></i> Cetak Label QR</button>
    <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i> Ubah</a>
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><h3 class="card-title">{{ $item->name }}</h3></div>
                @if ($item->photoUrl())
                    <img src="{{ $item->photoUrl() }}" alt="{{ $item->name }}" style="height:260px;object-fit:contain;width:100%;background:#f1f3f5">
                @endif
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5 col-md-3">Kode</dt><dd class="col-7 col-md-9"><code>{{ $item->code }}</code></dd>
                        <dt class="col-5 col-md-3">Kategori</dt><dd class="col-7 col-md-9">{{ $item->category->name }}</dd>
                        <dt class="col-5 col-md-3">Stok tersedia</dt><dd class="col-7 col-md-9">{{ format_number($item->stock) }}</dd>
                        <dt class="col-5 col-md-3">Kondisi</dt><dd class="col-7 col-md-9"><span class="badge {{ $item->conditionBadge() }}">{{ ucfirst($item->condition) }}</span></dd>
                        <dt class="col-5 col-md-3">Ditambahkan</dt><dd class="col-7 col-md-9 mb-0">{{ format_datetime($item->created_at) }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card d-print-none">
                <div class="card-header"><h3 class="card-title">Riwayat Peminjaman Terakhir</h3></div>
                <div class="table-responsive">
                    <table class="table table-vcenter card-table">
                        <thead><tr><th>Peminjam</th><th>Jumlah</th><th>Tanggal Pinjam</th><th>Batas Kembali</th><th>Status</th></tr></thead>
                        <tbody>
                        @forelse ($loans as $loan)
                            <tr>
                                <td>{{ $loan->user->name }}</td>
                                <td>{{ format_number($loan->quantity) }}</td>
                                <td>{{ format_date($loan->loan_date) }}</td>
                                <td>{{ format_date($loan->due_date) }}</td>
                                <td><span class="badge {{ $loan->statusBadge() }}">{{ ucfirst($loan->status) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">Barang ini belum pernah dipinjam.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">QR Code Barang</h3></div>
                <div class="card-body text-center qr-box">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->margin(1)->generate($item->code) !!}
                    <div class="mt-2 fw-bold">{{ $item->name }}</div>
                    <div class="text-secondary"><code>{{ $item->code }}</code></div>
                </div>
            </div>
        </div>
    </div>
@endsection
