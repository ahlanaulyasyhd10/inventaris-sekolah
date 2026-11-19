@extends('layouts.app')
@section('title', 'Cetak Label QR')
@section('breadcrumb')
    @include('partials.breadcrumb', ['items' => ['Barang' => route('admin.items.index'), 'Label QR' => null]])
@endsection
@section('actions')
    <button type="button" class="btn btn-primary" onclick="window.print()"><i class="ti ti-printer me-1"></i> Cetak</button>
@endsection

@section('content')
    <form method="GET" action="{{ route('admin.items.labels') }}" class="card mb-3 d-print-none">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="category">Filter kategori</label>
                <select id="category" name="category" class="form-select" autofocus>
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><button type="submit" class="btn btn-outline-primary"><i class="ti ti-filter me-1"></i> Terapkan</button></div>
            <div class="col-md-5 text-secondary small">{{ format_number($items->count()) }} label siap dicetak. Tempel label pada barang agar mudah dipindai.</div>
        </div>
    </form>

    <div class="row g-3">
        @foreach ($items as $item)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card card-sm text-center" style="break-inside: avoid">
                    <div class="card-body qr-box">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(1)->generate($item->code) !!}
                        <div class="fw-bold mt-2">{{ $item->name }}</div>
                        <div class="text-secondary"><code>{{ $item->code }}</code></div>
                        <div class="text-secondary small">{{ $item->category->name }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
