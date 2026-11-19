{{-- $action: URL, $message: teks konfirmasi --}}
<form action="{{ $action }}" method="POST" class="d-inline" data-confirm="{{ $message }}" data-confirm-button="Ya, hapus">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="ti ti-trash"></i></button>
</form>
