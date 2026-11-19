<div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="search">Cari barang</label>
                    <input type="text" id="search" class="form-control" placeholder="Nama atau kode barang" wire:model.live.debounce.300ms="search" autofocus>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="category">Kategori</label>
                    <select id="category" class="form-select" wire:model.live="category">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="condition">Kondisi</label>
                    <select id="condition" class="form-select" wire:model.live="condition">
                        <option value="">Semua</option>
                        @foreach ($conditions as $cond)
                            <option value="{{ $cond }}">{{ ucfirst($cond) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="availability">Ketersediaan</label>
                    <select id="availability" class="form-select" wire:model.live="availability">
                        <option value="">Semua</option>
                        <option value="tersedia">Stok tersedia</option>
                        <option value="habis">Stok habis</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn w-100" wire:click="resetFilters" title="Reset filter"><i class="ti ti-refresh"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cards" wire:loading.class="opacity-50">
        @forelse ($items as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card item-card h-100">
                    @if ($item->photoUrl())
                        <img src="{{ $item->photoUrl() }}" alt="{{ $item->name }}" class="card-img-top" style="height:170px;object-fit:contain;background:#f1f3f5">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-primary-lt text-primary card-img-top" style="height:170px"><i class="ti ti-photo" style="font-size:3rem"></i></div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start mb-2">
                            <div>
                                <h3 class="card-title mb-1">{{ $item->name }}</h3>
                                <div class="text-secondary"><code>{{ $item->code }}</code></div>
                            </div>
                            <span class="badge {{ $item->conditionBadge() }} ms-auto">{{ ucfirst($item->condition) }}</span>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-azure-lt">{{ $item->category->name }}</span>
                        </div>
                        <div class="mt-auto d-flex align-items-center">
                            <div>
                                <div class="text-secondary small">Stok tersedia</div>
                                <div class="h3 mb-0 {{ $item->stock > 0 ? '' : 'text-danger' }}">{{ format_number($item->stock) }}</div>
                            </div>
                            <div class="ms-auto">
                                @if ($item->isBorrowable())
                                    <a href="{{ route('my-loans.create', ['item' => $item->id]) }}" class="btn btn-primary btn-sm">Ajukan Pinjam</a>
                                @else
                                    <button type="button" class="btn btn-sm" disabled>{{ $item->stock > 0 ? 'Tidak dapat dipinjam' : 'Stok habis' }}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-secondary py-5">Barang tidak ditemukan. Coba ubah kata kunci atau filter.</div></div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">{{ $items->links() }}</div>
</div>
