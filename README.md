# Inventa - Sistem Inventaris Sekolah

Aplikasi web untuk mencatat barang inventaris sekolah beserta proses peminjaman dan pengembaliannya secara digital dan terpusat. Dibuat sebagai proyek **Web Fullstack** pada program magang Gamelab Indonesia.

Pengguna terbagi menjadi dua role:

- **Admin** (pengelola inventaris): mengelola kategori dan barang, memverifikasi peminjaman dan pengembalian, melihat activity log, serta mengelola akun user.
- **User** (guru/siswa): melihat daftar barang yang tersedia, mengajukan peminjaman, dan melihat riwayat peminjamannya sendiri.

## Tech Stack

| Komponen | Pilihan |
|---|---|
| Framework | Laravel 11 |
| View engine | Blade |
| Autentikasi | Laravel Breeze (login, register, logout, ubah kata sandi, lupa/reset kata sandi lewat email) |
| Database | MySQL |
| Tampilan | Tabler (Bootstrap 5), Tabler Icons, DataTables, ApexCharts, SweetAlert2 |
| Interaksi dinamis | Livewire 3 (membawa Alpine.js) |
| Role dan permission | Middleware role + spatie/laravel-permission |
| Ekspor, impor, QR | barryvdh/laravel-dompdf, maatwebsite/excel, simplesoftwareio/simple-qrcode |

Semua aset tampilan (CSS, JS, ikon, font) sudah ada di `public/vendor`, sehingga **Node.js tidak diperlukan**.

## Fitur

### Fitur wajib (sesuai brief)

- 6 tabel: `users` (dengan kolom `role`), `categories`, `items`, `loans`, `return_logs`, `activity_logs`, lengkap dengan relasi Eloquent (`hasMany`, `belongsTo`, `hasOne`) dan Soft Delete.
- CRUD kategori dan barang, peminjaman (Admin mencatat langsung, User mengajukan, stok otomatis berkurang), pengembalian dengan kondisi dan catatan, manajemen user (akun dan role), serta activity log (operasi CRUD dan aktivitas login/logout/register).
- Dashboard yang berbeda untuk Admin dan User, menu yang berbeda sesuai role.
- Seeder untuk semua tabel, Form Request Validation di seluruh form, DataTables dengan pencarian dan filter, angka dan tanggal berformat Indonesia, autofocus, placeholder, tanda bintang (*) pada field wajib, serta tampilan responsif.

### Pengembangan lanjutan (dari brief)

Alur persetujuan peminjaman oleh Admin, dashboard statistik (barang paling sering dipinjam, user paling aktif), grafik tren peminjaman per bulan, pencarian dan filter barang (Livewire), ekspor laporan peminjaman ke PDF dan Excel, QR Code barang, notifikasi di dalam aplikasi, serta role management dengan spatie/laravel-permission.

### Fitur tambahan

- **Notifikasi dalam aplikasi** (ikon lonceng): pengajuan menunggu, peminjaman terlambat, stok menipis, denda belum dibayar, dan pengingat jatuh tempo untuk User (tanpa email, muncul otomatis setiap halaman dibuka).
- **Lupa kata sandi mandiri**: User/Admin mengisi email di halaman Masuk, lalu menerima tautan reset (berlaku 60 menit). Email boleh memakai domain apa saja (`@gmail.com`, dll.); `@sekolah.test` hanya contoh akun seeder. Lihat bagian "Pengaturan email" di bawah. Admin juga tetap bisa mengganti kata sandi User lewat menu Manajemen User.
- **Perpanjangan peminjaman** lewat form: User memilih jumlah hari (maksimal 7 hari per perpanjangan) dan jumlah perpanjangan dibatasi lewat Pengaturan Sistem.
- **Denda keterlambatan**: dihitung otomatis per hari. Pembayaran dilakukan langsung ke Admin di sekolah, lalu Admin menandainya lunas.
- **Pengaturan sistem** (Admin): batas stok menipis, denda per hari, lama pinjam bawaan, dan maksimal perpanjangan.
- **Foto barang dan foto profil**, nomor WhatsApp user, ekspor/impor barang (Excel/CSV dengan template), bukti peminjaman PDF, cetak label QR, mode gelap, dan konfirmasi hapus dengan SweetAlert2.

## Prasyarat

- PHP 8.2 atau lebih baru (ekstensi `gd`, `zip`, `mbstring`, `pdo_mysql`, `fileinfo` aktif)
- Composer
- MySQL (XAMPP atau Laragon sudah termasuk)
- Git (untuk mengunduh dari GitHub)

## Instalasi

```bash
# 1. Ambil proyek
git clone https://github.com/USERNAME/NAMA-REPOSITORY.git
cd NAMA-REPOSITORY

# 2. Pasang dependensi PHP
composer install

# 3. Buat file konfigurasi dan kunci aplikasi
copy .env.example .env          # Linux/Mac: cp .env.example .env
php artisan key:generate
```

4. Nyalakan MySQL, lalu buat database kosong bernama `inventaris_sekolah` (misalnya lewat phpMyAdmin).
5. Buka `.env` dan sesuaikan `DB_USERNAME` serta `DB_PASSWORD` jika perlu (bawaan XAMPP: `root` dan password kosong).
6. Jalankan:

```bash
php artisan storage:link
php artisan migrate --seed
php artisan serve
```

Buka http://127.0.0.1:8000. Untuk mengulang dari awal (menghapus semua data): `php artisan migrate:fresh --seed`.

> File migration dan konfigurasi Spatie Permission sudah ada di dalam proyek, jadi tidak perlu menjalankan `vendor:publish`.

## Akun Hasil Seeder

Password semua akun: `password`

| Role | Email |
|---|---|
| Admin | admin@sekolah.test |
| User | maya@sekolah.test, ratih@sekolah.test, budi@sekolah.test, citra@sekolah.test, dian@sekolah.test, eko@sekolah.test, fitri@sekolah.test, gilang@sekolah.test |

## Data Seeder

1 admin dan 8 user, 8 kategori, 48 barang (dengan gambar bawaan), 32 peminjaman dengan semua status (menunggu, dipinjam, terlambat, dikembalikan, ditolak) yang tersebar 5 bulan terakhir, 19 data pengembalian, serta activity log yang dibuat dari alur peminjaman tersebut.

## Alur Peminjaman

1. **User mengajukan** peminjaman: status `menunggu`, stok barang langsung berkurang (dipesan).
2. **Admin memverifikasi**: disetujui menjadi `dipinjam` (stok tetap), atau ditolak menjadi `ditolak` (stok dikembalikan). Admin juga dapat mencatat peminjaman langsung dengan status `dipinjam`.
3. Setelah melewati batas kembali, status otomatis menjadi `terlambat` dan denda berjalan mulai dihitung.
4. **Admin mencatat pengembalian**: status `dikembalikan`, stok bertambah, kondisi dan catatan tersimpan di `return_logs`, dan denda (jika ada) dikunci.
5. Selama masih `dipinjam` dan belum lewat batas, User dapat memperpanjang peminjaman lewat form.

## Struktur Folder Utama

```
app/
  Http/Controllers/   Controller (Category, Item, Loan, ReturnLog, User, ActivityLog, Dashboard, dst.)
  Http/Middleware/    RoleMiddleware
  Http/Requests/      Form Request Validation
  Models/             User, Category, Item, Loan, ReturnLog, ActivityLog, Setting
  Livewire/           ItemCatalog (katalog barang dengan pencarian live)
  Exports/, Imports/  Ekspor dan impor Excel
  Support/            ActivityLogger
database/
  migrations/         Migration seluruh tabel
  seeders/            Seeder semua tabel dan data contoh
resources/views/      Blade per modul: categories, items, loans, return-logs, users, activity-logs, dst.
routes/               web.php (route aplikasi) dan auth.php (Breeze)
public/vendor/        Aset Tabler, DataTables, ApexCharts, SweetAlert2, jQuery
```

## Optimasi dan Keamanan

- **Optimasi query:** relasi dimuat dengan eager loading (`with`, `withCount`) di semua daftar sehingga tidak ada N+1; kolom yang sering difilter (`loans.status`, `due_date`, `loan_date`, `activity_logs.action`, `created_at`) diberi index; pembaruan stok memakai transaksi database dengan `lockForUpdate`.
- **Keamanan dasar:** token CSRF di semua form, validasi lewat Form Request, kata sandi dienkripsi (hash), pembatasan akses dengan middleware `role` dan permission, pembatasan percobaan login (throttle), validasi tipe dan ukuran berkas unggahan, serta Blade yang otomatis meng-escape keluaran. Pengguna hanya dapat membatalkan, memperpanjang, dan mengunduh bukti peminjaman miliknya sendiri.
- Untuk penggunaan di server sungguhan, ubah `APP_ENV=production` dan `APP_DEBUG=false` di `.env`.

## Catatan

- **Ukuran unggahan 5 MB:** pastikan `php.ini` memakai `upload_max_filesize = 5M` (atau lebih) dan `post_max_size = 8M` (atau lebih), lalu restart server.
- **Email hanya untuk reset kata sandi:** pengingat jatuh tempo memakai notifikasi lonceng, bukan email. Lihat bagian "Pengaturan email" untuk mengaktifkan pengiriman email sungguhan.
- **Scheduler (opsional):** `php artisan schedule:work` menandai peminjaman terlambat setiap hari. Status ini juga otomatis diperbarui setiap halaman terkait dibuka, jadi scheduler tidak wajib.

## Kredit

Template antarmuka: [Tabler](https://tabler.io) (lisensi MIT).

## Pengaturan email (untuk lupa kata sandi)

Secara bawaan `MAIL_MAILER=log`: email **tidak benar-benar terkirim**, isinya (termasuk tautan reset) ditulis ke `storage/logs/laravel.log`. Cocok untuk demo dan pengembangan lokal.

### Cara 1: ambil tautan dari file log (cepat, untuk demo)

1. Buka `http://127.0.0.1:8000/forgot-password` (atau klik "Lupa kata sandi?" di halaman Masuk).
2. Isi email akun yang sudah terdaftar (misalnya `admin@sekolah.test`), lalu klik **Kirim tautan reset**. Akan muncul pesan "Tautan reset kata sandi sudah dikirim ke email kamu".
3. Buka file `storage/logs/laravel.log` di VS Code, lalu tekan `Ctrl+End` untuk ke baris paling bawah, atau `Ctrl+F` dan cari `reset-password`.
4. Cari tautan berbentuk `http://127.0.0.1:8000/reset-password/xxxxxxxx?email=...` pada entri email yang paling baru.
5. Salin tautan itu ke browser, isi kata sandi baru dan konfirmasinya, lalu login dengan kata sandi baru.

Catatan:

- Tautan bisa terpotong ke beberapa baris. Sambungkan potongannya, hapus tanda `=` di ujung baris, dan ganti `=3D` menjadi `=`.
- Log menyimpan semua percobaan sebelumnya, jadi pakai tautan yang paling bawah (paling baru). Setiap permintaan baru membuat token baru dan token lama tidak berlaku.
- Tautan berlaku 60 menit dan hanya sekali pakai.
- Email yang diisi harus sudah terdaftar di tabel `users`. Jika belum, akan muncul pesan "Kami tidak menemukan pengguna dengan email tersebut".
- Untuk mengosongkan log sebelum demo, hapus isi file `storage/logs/laravel.log`.

### Cara 2: kirim email sungguhan lewat Gmail (Opsional)

Aktifkan verifikasi 2 langkah di akun Google, buat **App Password**, lalu ubah `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=null
MAIL_USERNAME=emailkamu@gmail.com
MAIL_PASSWORD=app-password-16-karakter
MAIL_FROM_ADDRESS="emailkamu@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Jalankan `php artisan config:clear` setelah mengubah `.env`. Tautan di email memakai alamat yang dipakai saat permintaan dibuat (mis. `127.0.0.1:8000`), jadi hanya bisa dibuka dari komputer yang menjalankan `php artisan serve`; di server sungguhan, isi `APP_URL` dengan domain aplikasi. Jangan upload `.env` ke GitHub.