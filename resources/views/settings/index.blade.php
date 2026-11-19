@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Pengaturan' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-8">
        <form class="card" method="POST" action="{{ route('admin.settings.update') }}" novalidate>
            @csrf
            @method('PUT')
            <div class="card-header"><h3 class="card-title">Aturan Peminjaman dan Stok</h3></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required" for="low_stock_threshold">Batas stok menipis (unit)</label>
                        <input type="number" min="0" id="low_stock_threshold" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror" placeholder="Contoh: 3" value="{{ old('low_stock_threshold', $values['low_stock_threshold']) }}" required autofocus>
                        <div class="form-hint">Barang dengan stok sama atau di bawah angka ini diberi peringatan.</div>
                        @error('low_stock_threshold') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required" for="late_fine_per_day">Denda keterlambatan per hari (Rp)</label>
                        <input type="number" min="0" step="500" id="late_fine_per_day" name="late_fine_per_day" class="form-control @error('late_fine_per_day') is-invalid @enderror" placeholder="Contoh: 1000" value="{{ old('late_fine_per_day', $values['late_fine_per_day']) }}" required>
                        <div class="form-hint">Dihitung otomatis saat pengembalian diverifikasi. Isi 0 untuk menonaktifkan denda.</div>
                        @error('late_fine_per_day') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required" for="default_loan_days">Lama pinjam bawaan (hari)</label>
                        <input type="number" min="1" id="default_loan_days" name="default_loan_days" class="form-control @error('default_loan_days') is-invalid @enderror" placeholder="Contoh: 7" value="{{ old('default_loan_days', $values['default_loan_days']) }}" required>
                        <div class="form-hint">Dipakai sebagai batas pengembalian awal pada form peminjaman.</div>
                        @error('default_loan_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required" for="max_extensions">Maksimal perpanjangan (kali)</label>
                        <input type="number" min="0" id="max_extensions" name="max_extensions" class="form-control @error('max_extensions') is-invalid @enderror" placeholder="Contoh: 1" value="{{ old('max_extensions', $values['max_extensions']) }}" required>
                        <div class="form-hint">Berapa kali satu peminjaman boleh diperpanjang. Isi 0 untuk menonaktifkan perpanjangan.</div>
                        @error('max_extensions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Simpan Pengaturan</button></div>
        </form>
    </div></div>
@endsection
