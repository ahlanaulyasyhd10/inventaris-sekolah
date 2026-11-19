@extends('layouts.app')
@section('title', 'Peminjaman')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Peminjaman' => null]])
@endsection
@section('actions')
    <a href="{{ route('admin.loans.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Catat Peminjaman</a>
@endsection

@section('content')
    {{-- Ekspor laporan --}}
    <div class="card mb-3">
        <div class="card-header"><h3 class="card-title">Ekspor Laporan Peminjaman</h3></div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.loans.export.pdf') }}" id="export-form" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="from">Dari tanggal pinjam</label>
                    <input type="date" id="from" name="from" class="form-control" placeholder="Tanggal awal" autofocus>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="to">Sampai tanggal pinjam</label>
                    <input type="date" id="to" name="to" class="form-control" placeholder="Tanggal akhir">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="export-status">Status</label>
                    <select id="export-status" name="status" class="form-select">
                        <option value="">Semua</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-danger"><i class="ti ti-file-type-pdf me-1"></i> PDF</button>
                    <button type="submit" class="btn btn-outline-success" formaction="{{ route('admin.loans.export.excel') }}"><i class="ti ti-file-spreadsheet me-1"></i> Excel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" data-table-filter="#loans-table" data-column="6" aria-label="Filter status">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-vcenter datatable" id="loans-table">
                <thead>
                <tr>
                    <th style="width:50px">No</th><th>Peminjam</th><th>Barang</th><th>Jumlah</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th>
                    <th class="no-sort text-end" style="width:250px">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($loans as $loan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->item->name }}</td>
                        <td data-order="{{ $loan->quantity }}">{{ format_number($loan->quantity) }}</td>
                        <td data-order="{{ $loan->loan_date->toDateString() }}">{{ format_date($loan->loan_date) }}</td>
                        <td data-order="{{ $loan->due_date->toDateString() }}">{{ format_date($loan->due_date) }}</td>
                        <td>
                            <span class="badge {{ $loan->statusBadge() }}">{{ ucfirst($loan->status) }}</span>
                            @if ($loan->accruedFine() > 0)
                                <div class="text-danger small mt-1">Denda berjalan {{ format_rupiah($loan->accruedFine()) }}</div>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            @if ($loan->status === \App\Models\Loan::STATUS_PENDING)
                                <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui"><i class="ti ti-check"></i></button>
                                </form>
                                <form action="{{ route('admin.loans.reject', $loan) }}" method="POST" class="d-inline" data-confirm="Pengajuan dari {{ $loan->user->name }} akan ditolak dan stok dikembalikan." data-confirm-button="Ya, tolak" data-confirm-variant="warning">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-warning" title="Tolak"><i class="ti ti-x"></i></button>
                                </form>
                            @endif
                            @if ($loan->isActive())
                                <a href="{{ route('admin.loans.return', $loan) }}" class="btn btn-sm btn-primary" title="Catat pengembalian"><i class="ti ti-arrow-back-up"></i></a>
                            @endif
                            <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-outline-secondary" title="Detail"><i class="ti ti-eye"></i></a>
                            @unless (in_array($loan->status, ['menunggu', 'ditolak']))
                                <a href="{{ route('admin.loans.receipt', $loan) }}" class="btn btn-sm btn-outline-secondary" title="Cetak bukti (PDF)"><i class="ti ti-printer"></i></a>
                            @endunless
                            @unless ($loan->isActive())
                                @include('partials.delete-button', ['action' => route('admin.loans.destroy', $loan), 'message' => 'Data peminjaman ini akan dihapus.'])
                            @endunless
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
