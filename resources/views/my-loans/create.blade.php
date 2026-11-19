@extends('layouts.app')
@section('title', 'Ajukan Peminjaman')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Peminjaman Saya' => route('my-loans.index'), 'Ajukan' => null]])
@endsection

@section('content')
    <div class="row"><div class="col-lg-7 mb-3">
        <form class="card" method="POST" action="{{ route('my-loans.store') }}" novalidate>
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label required" for="item_id">Barang</label>
                        <select id="item_id" name="item_id" class="form-select @error('item_id') is-invalid @enderror" required autofocus>
                            <option value="">-- Pilih barang --</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" data-photo="{{ $item->photoUrl() }}" data-name="{{ $item->name }}" data-code="{{ $item->code }}" data-category="{{ $item->category->name }}" data-stock="{{ $item->stock }}" data-condition="{{ ucfirst($item->condition) }}" @selected(old('item_id', $selectedItem) == $item->id)>{{ $item->name }} - {{ $item->category->name }} (stok {{ format_number($item->stock) }})</option>
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
                <div class="text-secondary small">Stok barang <strong>langsung berkurang</strong> saat pengajuan dikirim. Status pengajuan <strong>menunggu</strong> sampai diverifikasi Admin, dan stok dikembalikan jika ditolak atau dibatalkan.</div>
            </div>
            <div class="card-footer text-end">
                <a href="{{ route('my-loans.index') }}" class="btn btn-link">Batal</a>
                <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
    <div class="col-lg-5">
        <div class="card" id="item-preview" style="display:none">
            <div class="card-header"><h3 class="card-title">Barang yang dipilih</h3></div>
            <img id="preview-photo" src="" alt="" style="height:240px;object-fit:contain;width:100%;background:#f1f3f5;display:none">
            <div class="card-body">
                <h3 class="mb-1" id="preview-name"></h3>
                <div class="text-secondary mb-2"><code id="preview-code"></code> &middot; <span id="preview-category"></span></div>
                <div class="row">
                    <div class="col-6"><div class="text-secondary small">Stok tersedia</div><div class="h3 mb-0" id="preview-stock"></div></div>
                    <div class="col-6"><div class="text-secondary small">Kondisi</div><div class="h3 mb-0" id="preview-condition"></div></div>
                </div>
            </div>
        </div>
    </div></div>
@endsection

@push('scripts')
    <script>
        (function () {
            var select = document.getElementById('item_id');
            var qty = document.getElementById('quantity');
            function render() {
                var opt = select.options[select.selectedIndex];
                var card = document.getElementById('item-preview');
                if (!opt || !opt.value) { card.style.display = 'none'; return; }
                card.style.display = '';
                var img = document.getElementById('preview-photo');
                if (opt.dataset.photo) { img.src = opt.dataset.photo; img.alt = opt.dataset.name; img.style.display = ''; } else { img.style.display = 'none'; }
                document.getElementById('preview-name').textContent = opt.dataset.name;
                document.getElementById('preview-code').textContent = opt.dataset.code;
                document.getElementById('preview-category').textContent = opt.dataset.category;
                document.getElementById('preview-stock').textContent = parseInt(opt.dataset.stock, 10).toLocaleString('id-ID');
                document.getElementById('preview-condition').textContent = opt.dataset.condition;
                qty.max = opt.dataset.stock;
            }
            select.addEventListener('change', render);
            render();
        })();
    </script>
@endpush
