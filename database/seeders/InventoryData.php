<?php

namespace Database\Seeders;

/**
 * Data contoh bersama untuk seeder (kategori, barang, user, peminjaman).
 * Tanggal dibuat relatif terhadap hari ini supaya grafik dan status "terlambat" selalu terlihat wajar.
 */
class InventoryData
{
    public const PASSWORD = 'password';

    public static function categories(): array
    {
        return ['Alat Lab', 'Alat Olahraga', 'Elektronik', 'Alat Kebersihan', 'Alat Musik', 'Alat Peraga', 'Perlengkapan Kelas', 'Perlengkapan Pramuka'];
    }

    /** [kategori, nama, kode, stok total, kondisi] */
    public static function items(): array
    {
        return [
            ['Alat Lab', 'Mikroskop Binokuler', 'LAB-001', 12, 'baik'],
            ['Alat Lab', 'Gelas Ukur 500 ml', 'LAB-002', 30, 'baik'],
            ['Alat Lab', 'Tabung Reaksi', 'LAB-003', 100, 'rusak ringan'],
            ['Alat Lab', 'Bunsen Burner', 'LAB-004', 10, 'baik'],
            ['Alat Lab', 'Timbangan Digital', 'LAB-005', 6, 'baik'],
            ['Alat Lab', 'Rak Tabung Reaksi', 'LAB-006', 15, 'baik'],
            ['Alat Lab', 'Pipet Tetes', 'LAB-007', 50, 'baik'],
            ['Alat Lab', 'Kaca Pembesar', 'LAB-008', 20, 'baik'],
            ['Alat Lab', 'Termometer Lab', 'LAB-009', 14, 'rusak ringan'],

            ['Alat Olahraga', 'Bola Basket', 'OLR-001', 10, 'baik'],
            ['Alat Olahraga', 'Raket Badminton', 'OLR-002', 16, 'baik'],
            ['Alat Olahraga', 'Bola Voli', 'OLR-003', 8, 'rusak ringan'],
            ['Alat Olahraga', 'Bola Sepak', 'OLR-004', 9, 'baik'],
            ['Alat Olahraga', 'Bat Tenis Meja', 'OLR-005', 12, 'baik'],
            ['Alat Olahraga', 'Matras Senam', 'OLR-006', 10, 'baik'],
            ['Alat Olahraga', 'Tali Skipping', 'OLR-007', 20, 'baik'],
            ['Alat Olahraga', 'Cone Latihan', 'OLR-008', 30, 'baik'],

            ['Elektronik', 'Proyektor Epson', 'ELK-001', 4, 'baik'],
            ['Elektronik', 'Laptop Acer', 'ELK-002', 10, 'baik'],
            ['Elektronik', 'Speaker Portable', 'ELK-003', 2, 'rusak berat'],
            ['Elektronik', 'Layar Proyektor', 'ELK-004', 3, 'baik'],
            ['Elektronik', 'Kamera DSLR', 'ELK-005', 2, 'baik'],
            ['Elektronik', 'Mic Wireless', 'ELK-006', 5, 'baik'],
            ['Elektronik', 'Kabel HDMI', 'ELK-007', 15, 'baik'],
            ['Elektronik', 'Terminal Listrik', 'ELK-008', 12, 'rusak ringan'],

            ['Alat Kebersihan', 'Sapu Lantai', 'KBR-001', 20, 'baik'],
            ['Alat Kebersihan', 'Alat Pel Lantai', 'KBR-002', 12, 'baik'],
            ['Alat Kebersihan', 'Tempat Sampah Besar', 'KBR-003', 10, 'baik'],
            ['Alat Kebersihan', 'Sikat WC', 'KBR-004', 8, 'baik'],
            ['Alat Kebersihan', 'Kain Lap', 'KBR-005', 40, 'baik'],

            ['Alat Musik', 'Gitar Akustik', 'MSK-001', 6, 'baik'],
            ['Alat Musik', 'Keyboard Yamaha', 'MSK-002', 3, 'baik'],
            ['Alat Musik', 'Angklung Set', 'MSK-003', 5, 'baik'],
            ['Alat Musik', 'Rekorder Seruling', 'MSK-004', 30, 'baik'],
            ['Alat Musik', 'Drum Pad', 'MSK-005', 2, 'rusak ringan'],

            ['Alat Peraga', 'Globe Dunia', 'PRG-001', 6, 'baik'],
            ['Alat Peraga', 'Kerangka Manusia', 'PRG-002', 2, 'baik'],
            ['Alat Peraga', 'Peta Indonesia', 'PRG-003', 10, 'baik'],
            ['Alat Peraga', 'Model Tata Surya', 'PRG-004', 3, 'baik'],
            ['Alat Peraga', 'Torso Anatomi', 'PRG-005', 2, 'rusak ringan'],

            ['Perlengkapan Kelas', 'Papan Tulis Portable', 'KLS-001', 5, 'baik'],
            ['Perlengkapan Kelas', 'Spidol Whiteboard (Box)', 'KLS-002', 25, 'baik'],
            ['Perlengkapan Kelas', 'Penghapus Papan', 'KLS-003', 20, 'baik'],
            ['Perlengkapan Kelas', 'Kursi Lipat', 'KLS-004', 40, 'baik'],

            ['Perlengkapan Pramuka', 'Tenda Dome', 'PRM-001', 4, 'baik'],
            ['Perlengkapan Pramuka', 'Tongkat Pramuka', 'PRM-002', 30, 'baik'],
            ['Perlengkapan Pramuka', 'Tali Pionering', 'PRM-003', 15, 'baik'],
            ['Perlengkapan Pramuka', 'Kompas', 'PRM-004', 12, 'baik'],
        ];
    }

    /** [nama, email, role, no. WhatsApp] */
    public static function users(): array
    {
        return [
            ['Administrator', 'admin@sekolah.test', 'admin', '081200000001'],
            ['Maya Anggraini', 'maya@sekolah.test', 'user', '081200000002'],
            ['Ratih Pratiwi', 'ratih@sekolah.test', 'user', '081200000003'],
            ['Budi Santoso', 'budi@sekolah.test', 'user', '081200000004'],
            ['Citra Lestari', 'citra@sekolah.test', 'user', '081200000005'],
            ['Dian Permana', 'dian@sekolah.test', 'user', '081200000006'],
            ['Eko Prasetyo', 'eko@sekolah.test', 'user', '081200000007'],
            ['Fitri Handayani', 'fitri@sekolah.test', 'user', '081200000008'],
            ['Gilang Ramadhan', 'gilang@sekolah.test', 'user', '081200000009'],
        ];
    }

    /**
     * Data peminjaman.
     * Kunci: user (awal email), code, qty, ago (hari lalu), dur (lama pinjam), status,
     * ret (hari setelah pinjam, hanya untuk dikembalikan), cond, note, ext (jumlah perpanjangan).
     */
    public static function loans(): array
    {
        $r = fn ($u, $c, $q, $ago, $dur, $ret, $cond = 'baik', $note = null) => [
            'user' => $u, 'code' => $c, 'qty' => $q, 'ago' => $ago, 'dur' => $dur,
            'status' => 'dikembalikan', 'ret' => $ret, 'cond' => $cond, 'note' => $note, 'ext' => 0,
        ];
        $s = fn ($u, $c, $q, $ago, $dur, $status, $ext = 0) => [
            'user' => $u, 'code' => $c, 'qty' => $q, 'ago' => $ago, 'dur' => $dur,
            'status' => $status, 'ret' => null, 'cond' => null, 'note' => null, 'ext' => $ext,
        ];

        return [
            $r('maya', 'OLR-001', 2, 140, 7, 6, 'baik', 'Dikembalikan tepat waktu dalam kondisi baik.'),
            $r('ratih', 'LAB-001', 1, 128, 5, 5, 'baik', 'Dikembalikan lengkap tanpa kerusakan.'),
            $r('budi', 'ELK-001', 1, 118, 3, 3),
            $r('citra', 'MSK-001', 1, 105, 7, 8, 'baik', 'Terlambat 1 hari, senar masih lengkap.'),
            $r('dian', 'OLR-004', 3, 92, 4, 7, 'baik', 'Terlambat 3 hari.'),
            $r('eko', 'ELK-002', 1, 84, 7, 7),
            $r('fitri', 'PRG-001', 2, 75, 5, 5),
            $r('gilang', 'PRM-001', 1, 66, 7, 9, 'rusak ringan', 'Ada sobekan kecil pada kain tenda.'),
            $r('maya', 'LAB-001', 1, 58, 5, 5),
            $r('ratih', 'OLR-002', 2, 50, 7, 7),
            $r('budi', 'KLS-001', 1, 44, 3, 3),
            $r('citra', 'ELK-004', 1, 38, 2, 2),
            $r('dian', 'MSK-002', 1, 30, 7, 7),
            $r('eko', 'OLR-005', 4, 24, 5, 5),
            $r('fitri', 'LAB-005', 1, 19, 4, 4),
            $r('maya', 'OLR-001', 2, 15, 7, 7),
            $r('ratih', 'ELK-002', 1, 12, 5, 5),
            $r('gilang', 'PRM-004', 2, 9, 3, 3),
            $r('budi', 'LAB-001', 1, 8, 3, 3, 'rusak ringan', 'Lensa berdebu, sudah dibersihkan.'),

            $s('ratih', 'ELK-002', 1, 3, 7, 'dipinjam'),
            $s('maya', 'ELK-001', 1, 2, 7, 'dipinjam'),
            $s('dian', 'OLR-004', 2, 4, 6, 'dipinjam'),
            $s('eko', 'MSK-001', 1, 5, 7, 'dipinjam', 1),
            $s('citra', 'PRG-003', 1, 6, 7, 'dipinjam'),

            $s('ratih', 'OLR-001', 1, 14, 7, 'terlambat'),
            $s('gilang', 'ELK-002', 1, 10, 5, 'terlambat'),
            $s('budi', 'PRM-001', 1, 9, 4, 'terlambat'),

            $s('maya', 'ELK-005', 1, 0, 7, 'menunggu'),
            $s('dian', 'LAB-001', 2, 0, 5, 'menunggu'),
            $s('citra', 'ELK-004', 1, 1, 3, 'menunggu'),

            $s('ratih', 'KBR-001', 2, 4, 3, 'ditolak'),
            $s('eko', 'MSK-005', 1, 7, 3, 'ditolak'),
        ];
    }
}
