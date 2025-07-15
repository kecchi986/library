-- Tabel users (admin, petugas, anggota)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin','petugas','anggota') NOT NULL
);

-- Struktur tabel anggota perpustakaan
CREATE TABLE IF NOT EXISTS tb_anggota (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  no_ktp VARCHAR(50) NOT NULL,
  no_hp VARCHAR(20) NOT NULL,
  tgl_daftar DATE NOT NULL,
  tgl_keluar DATE
);

-- Struktur tabel buku
CREATE TABLE IF NOT EXISTS tb_buku (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  pengarang VARCHAR(100) NOT NULL,
  tahun_terbit INT NOT NULL,
  harga DECIMAL(10,2) NOT NULL
);

-- Struktur tabel fasilitas (barang tambahan)
CREATE TABLE IF NOT EXISTS tb_fasilitas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  harga DECIMAL(10,2) NOT NULL
);

-- Struktur tabel peminjaman buku
CREATE TABLE IF NOT EXISTS tb_peminjaman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_buku INT NOT NULL,
  id_anggota INT NOT NULL,
  tgl_pinjam DATE NOT NULL,
  tgl_kembali DATE,
  FOREIGN KEY (id_buku) REFERENCES tb_buku(id),
  FOREIGN KEY (id_anggota) REFERENCES tb_anggota(id)
);

-- Struktur tabel fasilitas yang digunakan anggota
CREATE TABLE IF NOT EXISTS tb_fasilitas_anggota (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_anggota INT NOT NULL,
  id_fasilitas INT NOT NULL,
  FOREIGN KEY (id_anggota) REFERENCES tb_anggota(id),
  FOREIGN KEY (id_fasilitas) REFERENCES tb_fasilitas(id)
);

-- Struktur tabel tagihan
CREATE TABLE IF NOT EXISTS tb_tagihan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bulan VARCHAR(20) NOT NULL,
  id_peminjaman INT NOT NULL,
  jml_tagihan DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_peminjaman) REFERENCES tb_peminjaman(id)
);

-- Struktur tabel pembayaran
CREATE TABLE IF NOT EXISTS tb_pembayaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_tagihan INT NOT NULL,
  jml_bayar DECIMAL(10,2) NOT NULL,
  status ENUM('lunas','cicil') NOT NULL,
  FOREIGN KEY (id_tagihan) REFERENCES tb_tagihan(id)
); 

-- Data contoh anggota
INSERT INTO tb_anggota (nama, no_ktp, no_hp, tgl_daftar) VALUES
('Budi Santoso', '1234567890', '08123456789', '2024-01-01'),
('Siti Aminah', '9876543210', '08234567890', '2024-02-01');

-- Data contoh buku
INSERT INTO tb_buku (judul, pengarang, tahun_terbit, harga) VALUES
('Pemrograman PHP', 'Andi', 2022, 75000),
('Dasar MySQL', 'Budi', 2021, 65000);

-- Data contoh fasilitas
INSERT INTO tb_fasilitas (nama, harga) VALUES
('Ruang Diskusi', 20000),
('Komputer', 15000);

-- Data contoh peminjaman
INSERT INTO tb_peminjaman (id_buku, id_anggota, tgl_pinjam, tgl_kembali) VALUES
(1, 1, '2024-06-01', NULL),
(2, 2, '2024-06-05', '2024-06-12');

-- Data contoh fasilitas anggota
INSERT INTO tb_fasilitas_anggota (id_anggota, id_fasilitas) VALUES
(1, 1),
(2, 2); 