{{--
    Kolom foto. Alur (sama untuk foto profil dan foto barang):
    - belum ada foto unggahan : tampil kolom pilih berkas (pratinjau/placeholder hanya sebagai gambar)
    - sudah ada foto unggahan : tampil gambar + tombol "Ubah" dan "Hapus" (hapus lewat pop-up konfirmasi)
    - setelah memilih berkas  : kolom pilih berkas hilang, tampil pratinjau + tombol "Ubah"
    Parameter: url, hasPhoto, shape (square|circle), height, hint, changeLabel, deleteFormId, deleteText, placeholderUser
--}}
@php
    $hasPhoto = (bool) ($hasPhoto ?? false);
    $previewUrl = $url ?? null;
    $circle = ($shape ?? 'square') === 'circle';
    $h = $height ?? 120;
    $showInput = ! $hasPhoto || $errors->has('photo');
    $showActions = $hasPhoto && ! $errors->has('photo');
    $imgClass = $circle ? 'rounded-circle border' : 'rounded border';
    $imgStyle = $circle ? "width:{$h}px;height:{$h}px;object-fit:cover" : "height:{$h}px;max-width:100%;object-fit:contain;background:#f1f3f5";
@endphp
<div class="photo-field">
    <div class="d-flex align-items-center flex-wrap gap-3">
        <div>
            @if (! $previewUrl)
                @isset($placeholderUser)
                    <span class="pf-placeholder">@include('partials.avatar', ['user' => $placeholderUser, 'size' => 'xl'])</span>
                @endisset
            @endif
            <img class="pf-preview {{ $imgClass }} {{ $previewUrl ? '' : 'd-none' }}" src="{{ $previewUrl }}" alt="Pratinjau foto" style="{{ $imgStyle }}">
        </div>
        <div class="flex-fill" style="min-width: 220px">
            <input type="file" name="photo" id="photo" accept="image/png,image/jpeg,image/webp" class="form-control pf-input @error('photo') is-invalid @enderror {{ $showInput ? '' : 'd-none' }}" placeholder="Pilih foto">
            @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="form-hint pf-hint {{ $showInput ? '' : 'd-none' }}">{{ $hint ?? 'Opsional. Format JPG, PNG, atau WEBP, maksimal 5 MB.' }}</div>
            <div class="btn-list pf-actions {{ $showActions ? '' : 'd-none' }}">
                <button type="button" class="btn btn-sm btn-outline-primary pf-change"><i class="ti ti-photo-edit me-1"></i> {{ $changeLabel ?? 'Ubah gambar' }}</button>
                @if ($hasPhoto && ! empty($deleteFormId))
                    <button type="button" class="btn btn-sm btn-outline-danger pf-delete" data-confirm-submit="#{{ $deleteFormId }}" data-confirm-text="{{ $deleteText ?? 'Foto akan dihapus.' }}" data-confirm-button="Ya, hapus"><i class="ti ti-trash me-1"></i> Hapus foto</button>
                @endif
            </div>
            <div class="form-hint text-primary pf-new d-none mt-2">Foto baru dipilih. Tekan <strong>Simpan</strong> untuk menyimpannya.</div>
        </div>
    </div>
</div>
