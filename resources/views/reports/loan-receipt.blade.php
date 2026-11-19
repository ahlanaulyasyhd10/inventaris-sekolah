<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Peminjaman #{{ $loan->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        .head { border-bottom: 2px solid #d63939; padding-bottom: 8px; margin-bottom: 14px; }
        h1 { font-size: 17px; margin: 0; }
        .sub { color: #555; }
        table.info { width: 100%; border-collapse: collapse; }
        table.info td { padding: 5px 4px; border-bottom: 1px solid #ddd; vertical-align: top; }
        table.info td.label { width: 34%; color: #555; }
        .badge { display: inline-block; padding: 2px 8px; border: 1px solid #333; border-radius: 4px; font-weight: bold; }
        .sign { width: 100%; margin-top: 40px; }
        .sign td { width: 50%; text-align: center; }
        .foot { margin-top: 28px; color: #777; font-size: 9px; }
    </style>
</head>
<body>
    <div class="head">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height:42px;float:right">
        <h1>BUKTI PEMINJAMAN BARANG</h1>
        <div class="sub">{{ config('app.name') }} &middot; Sistem Inventaris Sekolah &middot; No. #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</div>
    </div>

    <table class="info">
        <tr><td class="label">Peminjam</td><td>{{ $loan->user->name }} ({{ $loan->user->email }})</td></tr>
        <tr><td class="label">Barang</td><td>{{ $loan->item->name }} ({{ $loan->item->code }})</td></tr>
        <tr><td class="label">Kategori</td><td>{{ $loan->item->category->name ?? '-' }}</td></tr>
        <tr><td class="label">Jumlah</td><td>{{ format_number($loan->quantity) }} unit</td></tr>
        <tr><td class="label">Tanggal pinjam</td><td>{{ format_date($loan->loan_date) }}</td></tr>
        <tr><td class="label">Batas pengembalian</td><td>{{ format_date($loan->due_date) }}@if ($loan->extension_count) (diperpanjang {{ $loan->extension_count }}x)@endif</td></tr>
        <tr><td class="label">Status</td><td><span class="badge">{{ strtoupper($loan->status) }}</span></td></tr>
        @if ($loan->returnLog)
            <tr><td class="label">Tanggal kembali</td><td>{{ format_date($loan->returnLog->returned_at) }}</td></tr>
            <tr><td class="label">Kondisi saat kembali</td><td>{{ ucfirst($loan->returnLog->condition_on_return) }}</td></tr>
            <tr><td class="label">Denda</td><td>{{ $loan->returnLog->fine_amount > 0 ? format_rupiah($loan->returnLog->fine_amount).' ('.$loan->returnLog->late_days.' hari terlambat), '.($loan->returnLog->fine_paid_at ? 'LUNAS' : 'BELUM DIBAYAR') : 'Tidak ada' }}</td></tr>
        @endif
    </table>

    <table class="sign">
        <tr><td>Peminjam<br><br><br><br>( {{ $loan->user->name }} )</td><td>Pengelola Inventaris<br><br><br><br>( ........................ )</td></tr>
    </table>

    <div class="foot">Dicetak pada {{ format_datetime(now()) }}. Harap simpan bukti ini dan kembalikan barang sebelum batas pengembalian.</div>
</body>
</html>
