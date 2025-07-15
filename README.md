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
5. **Insert user awal** ke tabel `users` (lihat contoh di bawah).
6. **Akses** aplikasi via browser: `http://localhost/library/public/login.php`

## Skrip Insert User Awal
Jalankan di phpMyAdmin/SQL:
```sql
INSERT INTO users (username, password, nama, role) VALUES
('admin', MD5('admin123'), 'Administrator', 'admin'),
('petugas', MD5('petugas123'), 'Petugas Satu', 'petugas'),
('anggota', MD5('anggota123'), 'Anggota Satu', 'anggota');
```

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

**Saran:**
- Ganti password user setelah login pertama.
- Tambahkan validasi & keamanan sesuai kebutuhan produksi. 

Penyebab utama login selalu gagal meskipun username dan password sudah benar biasanya adalah:
1. **Password di database tidak terenkripsi MD5** (atau format hash tidak sama dengan yang dicek di login).
2. **Data user di tabel `users` tidak ada atau salah**.

### Analisis
- Di file `login.php`, password dicek dengan `MD5('$password')`.
- Di file `register.php` dan pada instruksi insert user awal di README, password memang sudah di-hash dengan MD5.
- Namun, jika Anda menginput user lewat phpMyAdmin tanpa MD5, atau copy-paste password biasa (bukan hash), login pasti gagal.

### Solusi
**Pastikan data user di tabel `users` sudah benar dan password-nya di-hash MD5.**

#### Contoh query insert user yang benar:
```sql
<code_block_to_apply_changes_from>
```
Jangan masukkan password dalam bentuk teks biasa!

---

### Langkah Perbaikan
1. **Cek data di tabel `users`**  
   Pastikan password sudah dalam bentuk hash MD5 (panjang 32 karakter, misal: `e99a18c428cb38d5f260853678922e03`).
2. **Jika perlu, hapus user lama dan insert ulang dengan query di atas.**
3. **Coba login lagi.**

---

Jika Anda ingin, saya bisa buatkan skrip PHP untuk menampilkan seluruh isi tabel `users` agar Anda bisa cek langsung dari browser. Apakah Anda ingin dibuatkan skrip pengecekan user, atau sudah paham langkah di atas? 