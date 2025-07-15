<?php
require_once '../config/database.php';
// 1. Buku yang tersedia (belum dipinjam)
$buku_tersedia = mysqli_query($conn, "SELECT * FROM tb_buku WHERE id NOT IN (SELECT id_buku FROM tb_peminjaman WHERE tgl_kembali IS NULL)");
// 2. Anggota yang harus segera mengembalikan buku (tgl_pinjam + 7 hari <= hari ini)
$segera_kembali = mysqli_query($conn, "SELECT a.nama, b.judul, p.tgl_pinjam, DATE_ADD(p.tgl_pinjam, INTERVAL 7 DAY) as batas FROM tb_peminjaman p JOIN tb_anggota a ON p.id_anggota=a.id JOIN tb_buku b ON p.id_buku=b.id WHERE p.tgl_kembali IS NULL AND DATE_ADD(p.tgl_pinjam, INTERVAL 7 DAY) = CURDATE()");
// 3. Anggota yang terlambat mengembalikan buku (tgl_pinjam + 7 hari < hari ini dan belum dikembalikan)
$terlambat = mysqli_query($conn, "SELECT a.nama, b.judul, p.tgl_pinjam, DATE_ADD(p.tgl_pinjam, INTERVAL 7 DAY) as batas FROM tb_peminjaman p JOIN tb_anggota a ON p.id_anggota=a.id JOIN tb_buku b ON p.id_buku=b.id WHERE p.tgl_kembali IS NULL AND DATE_ADD(p.tgl_pinjam, INTERVAL 7 DAY) < CURDATE()");
?>
<!DOCTYPE html>
<html>
<head><title>Perpustakaan - Halaman Depan</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Selamat Datang di Sistem Perpustakaan</h1>
<h2>Daftar Buku Tersedia</h2>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Judul</th><th>Pengarang</th><th>Tahun</th><th>Harga</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($buku_tersedia)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['judul']) ?></td>
<td><?= htmlspecialchars($row['pengarang']) ?></td>
<td><?= $row['tahun_terbit'] ?></td>
<td><?= number_format($row['harga'],0,',','.') ?></td>
</tr>
<?php endwhile; ?>
</table>
<div style="height:32px"></div>
<h2>Anggota yang Harus Segera Mengembalikan Buku</h2>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Nama</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($segera_kembali)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td><?= htmlspecialchars($row['judul']) ?></td>
<td><?= $row['tgl_pinjam'] ?></td>
<td><?= $row['batas'] ?></td>
</tr>
<?php endwhile; ?>
</table>
<div style="height:32px"></div>
<h2>Anggota yang Terlambat Mengembalikan Buku</h2>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Nama</th><th>Judul Buku</th><th>Tgl Pinjam</th><th>Batas Kembali</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($terlambat)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td><?= htmlspecialchars($row['judul']) ?></td>
<td><?= $row['tgl_pinjam'] ?></td>
<td><?= $row['batas'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html> 