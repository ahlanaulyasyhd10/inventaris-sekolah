<div class="card-body">
    <div class="mb-3">
        <label class="form-label required" for="name">Nama barang</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Proyektor Epson" value="{{ old('name', $item->name ?? '') }}" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label required" for="code">Kode / SKU</label>
            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Contoh: ELK-001" value="{{ old('code', $item->code ?? '') }}" required>
            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label required" for="category_id">Kategori</label>
            <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                <option value="">-- Pilih kategori --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label required" for="stock">Stok tersedia</label>
            <input type="number" id="stock" name="stock" min="0" class="form-control @error('stock') is-invalid @enderror" placeholder="Contoh: 10" value="{{ old('stock', $item->stock ?? '') }}" required>
            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label required" for="condition">Kondisi</label>
            <select id="condition" name="condition" class="form-select @error('condition') is-invalid @enderror" required>
                @foreach ($conditions as $condition)
                    <option value="{{ $condition }}" @selected(old('condition', $item->condition ?? 'baik') === $condition)>{{ ucfirst($condition) }}</option>
                @endforeach
            </select>
            @error('condition') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Foto barang</label>
        @include('partials.photo-field', [
            'url' => isset($item) ? $item->photoUrl() : null,
            'hasPhoto' => isset($item) && $item->photo,
            'deleteFormId' => (isset($item) && $item->photo) ? 'delete-photo-form' : null,
            'deleteText' => 'Foto barang akan dihapus dan diganti gambar bawaan sistem.',
            'changeLabel' => 'Ubah gambar',
        ])
    </div>
</div>
<div class="card-footer text-end">
    <a href="{{ route('admin.items.index') }}" class="btn btn-link">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
