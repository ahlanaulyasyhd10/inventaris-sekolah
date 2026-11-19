@extends('layouts.app')
@section('title', 'Catat Peminjaman')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Peminjaman' => route('admin.loans.index'), 'Catat' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-8">
        <form class="card" method="POST" action="{{ route('admin.loans.store') }}" novalidate>
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required" for="user_id">Peminjam</label>
                    <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required autofocus>
                        <option value="">-- Pilih peminjam --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label required" for="item_id">Barang</label>
                        <select id="item_id" name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">-- Pilih barang --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>{{ $item->name }} - {{ $item->category->name }} (stok {{ format_number($item->stock) }})</option>
                            @endforeach
                        </select>
                        @error('item_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required" for="quantity">Jumlah</label>
                        <input type="number" id="quantity" name="quantity" min="1" class="form-control @error('quantity') is-invalid @enderror" placeholder="Contoh: 1" value="{{ old('quantity', 1) }}" required>
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required" for="loan_date">Tanggal pinjam</label>
                        <input type="date" id="loan_date" name="loan_date" class="form-control @error('loan_date') is-invalid @enderror" placeholder="Pilih tanggal pinjam" value="{{ old('loan_date', now()->toDateString()) }}" required>
                        @error('loan_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required" for="due_date">Batas pengembalian</label>
                        <input type="date" id="due_date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" placeholder="Pilih batas pengembalian" value="{{ old('due_date', now()->addDays(setting('default_loan_days'))->toDateString()) }}" required>
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="text-secondary small">Peminjaman yang dicatat Admin langsung berstatus <strong>dipinjam</strong> dan stok barang otomatis berkurang.</div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('admin.loans.index') }}" class="btn btn-link">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div></div>
@endsection
