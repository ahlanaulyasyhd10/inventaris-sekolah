@extends('layouts.app')
@section('title', 'Barang')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Barang' => null]])
@endsection
@section('actions')
    <div class="dropdown">
        <a href="#" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-file-export me-1"></i> Ekspor / Impor</a>
        <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="{{ route('admin.items.export.excel') }}"><i class="ti ti-file-spreadsheet dropdown-item-icon"></i> Ekspor Excel</a>
            <a class="dropdown-item" href="{{ route('admin.items.export.pdf') }}"><i class="ti ti-file-type-pdf dropdown-item-icon"></i> Ekspor PDF</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#import-modal"><i class="ti ti-file-import dropdown-item-icon"></i> Impor dari Excel/CSV</a>
            <a class="dropdown-item" href="{{ route('admin.items.import.template') }}"><i class="ti ti-download dropdown-item-icon"></i> Unduh template impor</a>
        </div>
    </div>
    <a href="{{ route('admin.items.labels') }}" class="btn"><i class="ti ti-qrcode me-1"></i> Label QR</a>
    <a href="{{ route('admin.items.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i> Tambah Barang</a>
@endsection

@section('content')
    @if (session('import_errors'))
        <div class="alert alert-warning">
            <div class="fw-bold mb-1">Beberapa baris dilewati:</div>
            <ul class="mb-0">
                @foreach (session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body border-bottom py-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <select class="form-select" data-table-filter="#items-table" data-column="3" aria-label="Filter kategori">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" data-table-filter="#items-table" data-column="5" aria-label="Filter kondisi">
                        <option value="">Semua kondisi</option>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition }}">{{ ucfirst($condition) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-vcenter datatable" id="items-table">
                <thead>
                <tr>
                    <th style="width:50px">No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th><th>Kondisi</th>
                    <th class="no-sort text-end" style="width:170px">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><code>{{ $item->code }}</code></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if ($item->photoUrl())
                                    <span class="avatar avatar-sm me-2" style="background-image: url({{ $item->photoUrl() }})"></span>
                                @else
                                    <span class="avatar avatar-sm bg-primary-lt me-2"><i class="ti ti-photo"></i></span>
                                @endif
                                <span>{{ $item->name }}</span>
                            </div>
                        </td>
                        <td>{{ $item->category->name }}</td>
                        <td data-order="{{ $item->stock }}">{{ format_number($item->stock) }} @if ($item->isLowStock())<span class="badge bg-yellow-lt ms-1">Menipis</span>@endif</td>
                        <td><span class="badge {{ $item->conditionBadge() }}">{{ ucfirst($item->condition) }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.items.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="Detail & QR"><i class="ti ti-qrcode"></i></a>
                            <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Ubah"><i class="ti ti-edit"></i></a>
                            @include('partials.delete-button', ['action' => route('admin.items.destroy', $item), 'message' => 'Barang "'.$item->name.'" akan dihapus.'])
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <div class="modal modal-blur fade" id="import-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="modal-content" method="POST" action="{{ route('admin.items.import') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Impor Barang dari Excel / CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required" for="import-file">Berkas (.xlsx, .xls, .csv)</label>
                        <input type="file" id="import-file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".xlsx,.xls,.csv" placeholder="Pilih berkas" required>
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="text-secondary small">
                        Maksimal 5 MB. Kolom wajib pada baris pertama: <code>kode</code>, <code>nama barang</code>, <code>kategori</code>, <code>stok</code>, <code>kondisi</code>.
                        Barang dengan kode yang sudah ada akan diperbarui. Kategori baru dibuat otomatis.
                        <a href="{{ route('admin.items.import.template') }}">Unduh template</a>.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Impor</button>
                </div>
            </form>
        </div>
    </div>
    @if ($errors->has('file'))
        <script>document.addEventListener('DOMContentLoaded', function () { new bootstrap.Modal(document.getElementById('import-modal')).show(); });</script>
    @endif
@endpush
