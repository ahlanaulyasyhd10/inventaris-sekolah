@extends('layouts.app')
@section('title', 'Detail Peminjaman')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Peminjaman' => route('admin.loans.index'), 'Detail' => null]])
@endsection
@section('actions')
    @unless (in_array($loan->status, ['menunggu', 'ditolak']))
        <a href="{{ route('admin.loans.receipt', $loan) }}" class="btn"><i class="ti ti-printer me-1"></i> Cetak Bukti</a>
    @endunless
    @if ($loan->isActive())
        <a href="{{ route('admin.loans.return', $loan) }}" class="btn btn-primary"><i class="ti ti-arrow-back-up me-1"></i> Catat Pengembalian</a>
    @endif
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Peminjaman</h3>
                    <div class="card-actions"><span class="badge {{ $loan->statusBadge() }}">{{ ucfirst($loan->status) }}</span></div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Peminjam</dt><dd class="col-7">{{ $loan->user->name }} <span class="text-secondary">({{ $loan->user->email }})</span>
                            @if (whatsapp_url($loan->user->phone))
                                <div><a href="{{ whatsapp_url($loan->user->phone) }}" target="_blank" rel="noopener"><i class="ti ti-brand-whatsapp"></i> {{ $loan->user->phone }}</a></div>
                            @endif
                        </dd>
                        <dt class="col-5">Barang</dt><dd class="col-7">{{ $loan->item->name }} <code>{{ $loan->item->code }}</code></dd>
                        <dt class="col-5">Kategori</dt><dd class="col-7">{{ $loan->item->category->name ?? '-' }}</dd>
                        <dt class="col-5">Jumlah</dt><dd class="col-7">{{ format_number($loan->quantity) }}</dd>
                        <dt class="col-5">Tanggal pinjam</dt><dd class="col-7">{{ format_date($loan->loan_date) }}</dd>
                        <dt class="col-5">Batas pengembalian</dt><dd class="col-7">{{ format_date($loan->due_date) }}</dd>
                        @if ($loan->accruedFine() > 0)
                            <dt class="col-5">Denda berjalan</dt><dd class="col-7 text-danger">{{ format_rupiah($loan->accruedFine()) }} <span class="text-secondary">({{ format_number($loan->daysLate()) }} hari terlambat)</span></dd>
                        @endif
                        <dt class="col-5">Perpanjangan</dt><dd class="col-7 mb-0">{{ $loan->extension_count ? format_number($loan->extension_count).' kali' : 'Belum pernah' }}</dd>
                    </dl>
                </div>
                @if ($loan->status === \App\Models\Loan::STATUS_PENDING)
                    <div class="card-footer text-end">
                        <form action="{{ route('admin.loans.reject', $loan) }}" method="POST" class="d-inline" data-confirm="Pengajuan ini akan ditolak dan stok dikembalikan." data-confirm-button="Ya, tolak" data-confirm-variant="warning">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-warning"><i class="ti ti-x me-1"></i> Tolak</button>
                        </form>
                        <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i> Setujui</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Data Pengembalian</h3></div>
                <div class="card-body">
                    @if ($loan->returnLog)
                        <dl class="row mb-0">
                            <dt class="col-6">Tanggal kembali</dt><dd class="col-6">{{ format_date($loan->returnLog->returned_at) }}</dd>
                            <dt class="col-6">Kondisi</dt><dd class="col-6">{{ ucfirst($loan->returnLog->condition_on_return) }}</dd>
                            <dt class="col-6">Keterlambatan</dt><dd class="col-6">{{ $loan->returnLog->lateDays() > 0 ? format_number($loan->returnLog->lateDays()).' hari' : 'Tepat waktu' }}</dd>
                            <dt class="col-6">Denda</dt><dd class="col-6">
                                @if ($loan->returnLog->fine_amount > 0)
                                    {{ format_rupiah($loan->returnLog->fine_amount) }}
                                    <span class="badge {{ $loan->returnLog->fine_paid_at ? 'bg-green-lt' : 'bg-red-lt' }}">{{ $loan->returnLog->fine_paid_at ? 'Lunas' : 'Belum dibayar' }}</span>
                                @else
                                    -
                                @endif
                            </dd>
                            <dt class="col-6">Catatan</dt><dd class="col-6 mb-0">{{ $loan->returnLog->notes ?: '-' }}</dd>
                        </dl>
                    @else
                        <div class="text-secondary">Barang belum dikembalikan.</div>
                        @if ($loan->accruedFine() > 0)
                            <div class="text-danger small mt-2">Denda masih berjalan setiap hari dan baru dikunci saat pengembalian dicatat.</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
