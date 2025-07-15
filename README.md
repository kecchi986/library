# Sistem Perpustakaan PHP Native

Sistem ini terdiri dari 3 peran utama: Admin, Petugas, dan Anggota. Fitur utama meliputi manajemen buku, anggota, petugas, transaksi peminjaman/pengembalian, laporan, dan katalog buku.

## Fitur Utama
- Login multi-role (admin, petugas, anggota)
- Manajemen data buku (CRUD)
- Manajemen data anggota (CRUD)
- Manajemen data petugas (CRUD, khusus admin)
- Transaksi peminjaman & pengembalian buku (petugas)
- Laporan peminjaman & pengembalian (admin)
- Katalog & pencarian buku (anggota)
- Riwayat peminjaman anggota
- Dashboard sesuai role
- UI modern dan responsif (CSS custom)
- Password terenkripsi aman (bcrypt)

## Struktur Folder
- config/ : Konfigurasi database & SQL
- public/ : Semua file PHP utama (login, dashboard, CRUD, transaksi, dll)
- assets/ : CSS & JS
- templates/ : Header & Footer

## Cara Instalasi
1. **Clone/copy** project ke folder web server (misal: `htdocs/library` di XAMPP).
2. **Buat database** baru di MySQL, misal: `library_db`.
3. **Import** file `config/database.sql` ke database tersebut (bisa via phpMyAdmin).
4. **Edit** file `config/database.php` jika user/password MySQL Anda berbeda.
5. **Akses** aplikasi via browser: `http://localhost/library/public/login.php`
6. **Buat user admin** dengan register, atau gunakan script reset password admin di bawah.
6. **Generate tagihan** terlebih dahulu di menu "Tagihan" sebelum melakukan pembayaran. Jika belum ada tagihan, pembayaran tidak dapat dilakukan.

## Reset Password Admin (Darurat)
Jika lupa password admin, gunakan script berikut:
1. Buka `http://localhost/library/public/reset_admin.php` di browser.
2. Klik tombol **Jalankan Reset** untuk mengatur password admin menjadi `admin123`.
3. Setelah berhasil, **hapus file reset_admin.php** demi keamanan.

## Menambah User Manual (phpMyAdmin/SQL)
**Disarankan menambah user lewat menu register/admin.**
Jika ingin insert manual, gunakan hash bcrypt hasil dari PHP:
```php
php -r "echo password_hash('password_baru', PASSWORD_DEFAULT);"
```
Lalu masukkan ke query berikut:
```sql
INSERT INTO users (username, password, nama, role) VALUES
('admin', '$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'Administrator', 'admin');
```
Ganti hash dengan hasil dari perintah PHP di atas.

## Catatan
- Untuk menambah anggota, gunakan menu "Manajemen Anggota" (admin/petugas).
- Untuk menambah petugas, gunakan menu "Manajemen Petugas" (admin).
- Untuk menambah buku, gunakan menu "Manajemen Buku" (admin/petugas).
- Untuk transaksi, login sebagai petugas.
- Untuk katalog & riwayat, login sebagai anggota.

## Kustomisasi
- CSS dapat diubah di `assets/css/style.css`.
- Header/footer di `templates/`.

---

**Tips Keamanan:**
- Ganti password user setelah login pertama.
- Hapus file reset_admin.php setelah digunakan.
- Tambahkan validasi & keamanan sesuai kebutuhan produksi.

## Troubleshooting Login
- Pastikan password di database sudah hash bcrypt (panjang 60 karakter, diawali `$2y$`).
- Jika login gagal, reset password admin dengan script reset_admin.php.
- Jika register user baru bisa login, berarti user lama perlu reset password.
- Kolom password di tabel `users` harus VARCHAR(255).

---

Jika ada kendala, silakan cek error log PHP atau hubungi pengembang. 

## Alur Tagihan, Denda, dan Pembayaran
- Denda (keterlambatan) dihitung otomatis saat generate tagihan di menu "Tagihan".
- Denda dan total tagihan akan tersimpan di database dan digunakan pada proses pembayaran.
- Pembayaran hanya bisa dilakukan jika tagihan sudah digenerate.
- Jika belum ada tagihan, sistem akan menampilkan peringatan di menu pembayaran.
- Denda yang tampil di pembayaran/tagihan adalah denda yang sudah dihitung dan disimpan saat generate tagihan, bukan denda real-time dari peminjaman. 