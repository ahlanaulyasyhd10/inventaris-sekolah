<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Data Barang</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; }
        th { background: #fbe4e4; }
    </style>
</head>
<body>
    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:36px;float:right">
    <h1>Laporan Data Barang Inventaris</h1>
    <div class="meta">Dicetak: {{ format_datetime(now()) }} &middot; Total barang: {{ format_number($items->count()) }} &middot; Total stok: {{ format_number($items->sum('stock')) }}</div>
    <table>
        <thead><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th><th>Kondisi</th></tr></thead>
        <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->code }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? '-' }}</td>
                <td>{{ format_number($item->stock) }}</td>
                <td>{{ ucfirst($item->condition) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
