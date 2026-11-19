<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 4px 6px; text-align: left; }
        th { background: #e8eefc; }
    </style>
</head>
<body>
    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:36px;float:right">
    <h1>Laporan Peminjaman Inventaris Sekolah</h1>
    <div class="meta">
        Dicetak: {{ format_datetime(now()) }}
        @if ($from || $to) &middot; Periode pinjam: {{ $from ? format_date($from) : 'awal' }} s.d. {{ $to ? format_date($to) : 'sekarang' }} @endif
        @if ($status) &middot; Status: {{ ucfirst($status) }} @endif
        &middot; Total: {{ format_number($loans->count()) }} data
    </div>
    <table>
        <thead>
        <tr>
            <th>No</th><th>Peminjam</th><th>Barang</th><th>Kategori</th><th>Jumlah</th>
            <th>Tgl Pinjam</th><th>Batas Kembali</th><th>Tgl Kembali</th><th>Kondisi Kembali</th><th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($loans as $loan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $loan->user->name }}</td>
                <td>{{ $loan->item->name }} ({{ $loan->item->code }})</td>
                <td>{{ $loan->item->category->name ?? '-' }}</td>
                <td>{{ format_number($loan->quantity) }}</td>
                <td>{{ format_date($loan->loan_date) }}</td>
                <td>{{ format_date($loan->due_date) }}</td>
                <td>{{ $loan->returnLog ? format_date($loan->returnLog->returned_at) : '-' }}</td>
                <td>{{ $loan->returnLog->condition_on_return ?? '-' }}</td>
                <td>{{ ucfirst($loan->status) }}</td>
            </tr>
        @empty
            <tr><td colspan="10" style="text-align:center">Tidak ada data.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
