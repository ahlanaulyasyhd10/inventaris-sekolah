@extends('layouts.app')
@section('title', 'Peminjaman Saya')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Peminjaman Saya' => null]])
@endsection
@section('actions')
    <a href="{{ route('my-loans.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Ajukan Peminjaman</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" data-table-filter="#my-loans-table" data-column="5" aria-label="Filter status">
                        <option value="">Semua status</option>
                        @foreach (\App\Models\Loan::STATUSES as $status)
                            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-vcenter datatable" id="my-loans-table">
                <thead>
                <tr><th style="width:50px">No</th><th>Barang</th><th>Jumlah</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th><th>Tgl Kembali</th><th>Denda</th><th class="no-sort text-end" style="width:170px">Aksi</th></tr>
                </thead>
                <tbody>
                @foreach ($loans as $loan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
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
                        <td>{{ $loan->returnLog ? format_date($loan->returnLog->returned_at) : '-' }}</td>
                        <td>
                            @if ($loan->returnLog && $loan->returnLog->fine_amount > 0)
                                <div>{{ format_rupiah($loan->returnLog->fine_amount) }}</div>
                                @if ($loan->returnLog->fine_paid_at)
                                    <span class="badge bg-green-lt">Lunas</span>
                                @else
                                    <span class="badge bg-red-lt">Belum dibayar</span>
                                    <div class="text-secondary small mt-1">Bayar langsung ke Admin</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-end">
                            @unless (in_array($loan->status, ['menunggu', 'ditolak']))
                                <a href="{{ route('my-loans.receipt', $loan) }}" class="btn btn-sm btn-outline-secondary" title="Unduh bukti (PDF)"><i class="ti ti-printer"></i></a>
                            @endunless
                            @if ($loan->canBeExtended())
                                <button type="button" class="btn btn-sm btn-outline-primary" title="Perpanjang" data-extend data-action="{{ route('my-loans.extend', $loan) }}" data-item="{{ $loan->item->name }}" data-due="{{ $loan->due_date->toDateString() }}"><i class="ti ti-calendar-plus"></i></button>
                            @endif
                            @if ($loan->status === \App\Models\Loan::STATUS_PENDING)
                                @include('partials.delete-button', ['action' => route('my-loans.destroy', $loan), 'message' => 'Pengajuan peminjaman akan dibatalkan.'])
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('partials.extend-modal')
@endsection
