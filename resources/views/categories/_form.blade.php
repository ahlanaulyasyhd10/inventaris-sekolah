<div class="card-body">
    <div class="mb-3">
        <label class="form-label required" for="name">Nama kategori</label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Alat Lab" value="{{ old('name', $category->name ?? '') }}" required autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
<div class="card-footer text-end">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-link">Batal</a>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>
