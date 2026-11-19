@extends('layouts.app')
@section('title', 'Pengembalian')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Pengembalian' => null]])
@endsection

@section('content')
    <div class="card">
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" data-table-filter="#return-table" data-column="5" aria-label="Filter kondisi">
                        <option value="">Semua kondisi</option>
                        @foreach (\App\Models\Item::CONDITIONS as $condition)
                            <option value="{{ $condition }}">{{ ucfirst($condition) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="denda-filter" class="form-select" data-table-filter="#return-table" data-column="6" aria-label="Filter status denda">
                        <option value="">Semua status denda</option>
                        <option value="Belum dibayar">Denda belum dibayar</option>
                        <option value="Lunas">Denda lunas</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-vcenter datatable" id="return-table">
                <thead>
                <tr><th style="width:50px">No</th><th>Peminjam</th><th>Barang</th><th>Tgl Kembali</th><th>Keterlambatan</th><th>Kondisi</th><th>Denda</th><th>Catatan</th><th class="no-sort text-end" style="width:140px">Aksi</th></tr>
                </thead>
                <tbody>
                @foreach ($returnLogs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $log->loan->user->name }}</td>
                        <td>{{ $log->loan->item->name }} <span class="text-secondary">&times; {{ format_number($log->loan->quantity) }}</span></td>
                        <td data-order="{{ $log->returned_at->toDateString() }}">{{ format_date($log->returned_at) }}</td>
                        <td>
                            @if ($log->lateDays() > 0)
                                <span class="badge bg-red-lt">{{ format_number($log->lateDays()) }} hari</span>
                            @else
                                <span class="badge bg-green-lt">Tepat waktu</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $log->condition_on_return === 'baik' ? 'bg-green-lt' : ($log->condition_on_return === 'rusak ringan' ? 'bg-yellow-lt' : 'bg-red-lt') }}">{{ ucfirst($log->condition_on_return) }}</span></td>
                        <td data-order="{{ $log->fine_amount }}">
                            @if ($log->fine_amount > 0)
                                <div>{{ format_rupiah($log->fine_amount) }}</div>
                                @if ($log->fine_paid_at)
                                    <span class="badge bg-green-lt">Lunas {{ format_date($log->fine_paid_at) }}</span>
                                @else
                                    <span class="badge bg-red-lt">Belum dibayar</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $log->notes ?: '-' }}</td>
                        <td class="text-end">
                            @if ($log->hasUnpaidFine())
                                <form action="{{ route('admin.return-logs.pay', $log) }}" method="POST" class="d-inline" data-confirm="Tandai denda {{ format_rupiah($log->fine_amount) }} dari {{ $log->loan->user->name }} sebagai lunas? Pastikan pembayaran sudah diterima." data-confirm-button="Ya, tandai lunas" data-confirm-variant="success" data-confirm-icon="question">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success"><i class="ti ti-cash me-1"></i> Tandai Lunas</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            // Dibuka dari kartu denda di dashboard: langsung tampilkan denda yang belum dibayar.
            var filter = new URLSearchParams(window.location.search).get('denda');
            if (filter) { $('#denda-filter').val(filter).trigger('change'); }
        });
    </script>
@endpush
