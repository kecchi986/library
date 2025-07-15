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
  harga DECIMAL(10,2) NOT NULL,
  stok INT NOT NULL DEFAULT 0
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
  denda DECIMAL(10,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (id_peminjaman) REFERENCES tb_peminjaman(id)
);

-- Struktur tabel pembayaran
CREATE TABLE IF NOT EXISTS tb_pembayaran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_tagihan INT NOT NULL,
  jml_bayar DECIMAL(10,2) NOT NULL,
  status ENUM('lunas','cicil') NOT NULL,
  keterangan VARCHAR(50),
  FOREIGN KEY (id_tagihan) REFERENCES tb_tagihan(id)
); 

-- Data contoh anggota
INSERT INTO tb_anggota (nama, no_ktp, no_hp, tgl_daftar) VALUES
('Rizky Maulana', '3201010101010001', '081234567890', '2024-01-10'),
('Intan Permata', '3201020202020002', '082134567891', '2024-02-15'),
('Fajar Nugroho', '3201030303030003', '083134567892', '2024-03-20'),
('Salsa Amelia', '3201040404040004', '084134567893', '2024-04-25');

-- Data contoh buku
INSERT INTO tb_buku (judul, pengarang, tahun_terbit, harga, stok) VALUES
('Belajar PHP untuk Pemula', 'Rizal Ramli', 2021, 85000, 7),
('MySQL: Panduan Praktis', 'Siti Nurhaliza', 2020, 70000, 5),
('Struktur Data & Algoritma', 'Bambang Pamungkas', 2019, 95000, 3),
('Jaringan Komputer Modern', 'Dewi Sartika', 2022, 120000, 4);

-- Data contoh fasilitas
INSERT INTO tb_fasilitas (nama, harga) VALUES
('Ruang Baca AC', 25000),
('Komputer Multimedia', 20000),
('Printer Warna', 15000);

-- Data contoh peminjaman
INSERT INTO tb_peminjaman (id_buku, id_anggota, tgl_pinjam, tgl_kembali) VALUES
(1, 1, '2024-06-01', NULL),
(2, 2, '2024-06-05', '2024-06-12'),
(3, 3, '2024-06-10', NULL),
(4, 4, '2024-06-02', '2024-06-09');

-- Data contoh fasilitas anggota
INSERT INTO tb_fasilitas_anggota (id_anggota, id_fasilitas) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 1);

-- Data contoh tagihan
INSERT INTO tb_tagihan (bulan, id_peminjaman, jml_tagihan, denda) VALUES
('Juni 2024', 1, 85000, 0),
('Juni 2024', 2, 70000, 0),
('Juni 2024', 3, 95000, 0),
('Juni 2024', 4, 120000, 0);

-- Data contoh pembayaran
INSERT INTO tb_pembayaran (id_tagihan, jml_bayar, status) VALUES
(1, 85000, 'lunas'),
(2, 35000, 'cicil'),
(3, 95000, 'lunas'),
(4, 120000, 'lunas'); 