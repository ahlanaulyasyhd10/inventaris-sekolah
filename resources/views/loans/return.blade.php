@extends('layouts.app')
@section('title', 'Catat Pengembalian')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Peminjaman' => route('admin.loans.index'), 'Pengembalian' => null]])
@endsection

@section('content')
    <div class="row row-cards">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Ringkasan Peminjaman</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Peminjam</dt><dd class="col-7">{{ $loan->user->name }}</dd>
                        <dt class="col-5">Barang</dt><dd class="col-7">{{ $loan->item->name }}</dd>
                        <dt class="col-5">Jumlah</dt><dd class="col-7">{{ format_number($loan->quantity) }}</dd>
                        <dt class="col-5">Tanggal pinjam</dt><dd class="col-7">{{ format_date($loan->loan_date) }}</dd>
                        <dt class="col-5">Batas kembali</dt><dd class="col-7">{{ format_date($loan->due_date) }}</dd>
                        <dt class="col-5">Status</dt><dd class="col-7 mb-0"><span class="badge {{ $loan->statusBadge() }}">{{ ucfirst($loan->status) }}</span></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <form class="card" method="POST" action="{{ route('admin.loans.return.store', $loan) }}" novalidate>
                @csrf
                <div class="card-header"><h3 class="card-title">Form Pengembalian</h3></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required" for="returned_at">Tanggal pengembalian</label>
                        <input type="date" id="returned_at" name="returned_at" class="form-control @error('returned_at') is-invalid @enderror" placeholder="Pilih tanggal pengembalian" value="{{ old('returned_at', now()->toDateString()) }}" required autofocus>
                        @error('returned_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="condition_on_return">Kondisi saat dikembalikan</label>
                        <select id="condition_on_return" name="condition_on_return" class="form-select @error('condition_on_return') is-invalid @enderror" required>
                            @foreach ($conditions as $condition)
                                <option value="{{ $condition }}" @selected(old('condition_on_return', 'baik') === $condition)>{{ ucfirst($condition) }}</option>
                            @endforeach
                        </select>
                        @error('condition_on_return') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="notes">Catatan</label>
                        <textarea id="notes" name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="alert alert-info mb-0" id="fine-preview" data-due="{{ $loan->due_date->toDateString() }}" data-rate="{{ setting('late_fine_per_day') }}">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-receipt-2 fs-2 me-2"></i>
                            <div>Denda keterlambatan: <strong id="fine-amount">Rp 0</strong> <span id="fine-days" class="text-secondary"></span></div>
                        </div>
                    </div>
                    <div class="mt-3" id="pay-now-box" style="display:none">
                        <label class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="pay_now" value="1" @checked(old('pay_now'))>
                            <span class="form-check-label">Denda sudah dibayar langsung ke Admin (tandai lunas sekarang)</span>
                        </label>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.loans.index') }}" class="btn btn-link">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var box = document.getElementById('fine-preview');
            var input = document.getElementById('returned_at');
            if (!box || !input) { return; }
            var due = new Date(box.dataset.due + 'T00:00:00');
            var rate = parseInt(box.dataset.rate, 10) || 0;
            function render() {
                var ret = new Date(input.value + 'T00:00:00');
                var days = isNaN(ret) ? 0 : Math.max(0, Math.round((ret - due) / 86400000));
                document.getElementById('fine-amount').textContent = 'Rp ' + (days * rate).toLocaleString('id-ID');
                document.getElementById('pay-now-box').style.display = days > 0 ? '' : 'none';
                document.getElementById('fine-days').textContent = days > 0 ? '(' + days + ' hari terlambat x Rp ' + rate.toLocaleString('id-ID') + ')' : '(tepat waktu)';
            }
            input.addEventListener('input', render);
            render();
        })();
    </script>
@endpush
