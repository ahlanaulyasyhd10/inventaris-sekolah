<?php

/*
| Nilai bawaan pengaturan sistem. Admin dapat mengubahnya lewat menu Pengaturan
| (disimpan di tabel settings dan menimpa nilai di bawah ini).
*/
return [
    'low_stock_threshold' => 3,     // stok <= nilai ini dianggap menipis
    'late_fine_per_day' => 1000,    // denda keterlambatan per hari (Rupiah)
    'default_loan_days' => 7,       // lama pinjam bawaan di form
    // Aturan perpanjangan (tidak ada di menu Pengaturan; User memilih jumlah hari lewat form):
    'extension_days' => 7,          // batas maksimal tambahan hari per perpanjangan
    'max_extensions' => 1,          // maksimal perpanjangan per peminjaman
];
