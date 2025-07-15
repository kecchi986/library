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
<head>
<title>Perpustakaan - Halaman Depan</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: linear-gradient(135deg, #2980b9 0%, #6dd5fa 100%); min-height:100vh;">
<div style="display:flex;justify-content:center;align-items:center;min-height:100vh;">
<div style="background:#fff;max-width:900px;width:100%;border-radius:18px;box-shadow:0 2px 16px #0002;padding:40px 32px;">
    <div style="text-align:center;margin-bottom:32px;">
        <div style="font-size:2.3rem;color:#2980b9;font-weight:700;letter-spacing:1px;"><i class="fa fa-book"></i> Selamat Datang di Sistem Perpustakaan</div>
        <div style="font-size:1.1rem;color:#6c7a89;margin-top:8px;">Informasi Buku & Peminjaman</div>
    </div>
    <h2 style="margin-top:0;"><i class="fa fa-book-open"></i> Daftar Buku Tersedia</h2>
    <table style="box-shadow:0 1px 8px #0001;">
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
    <h2><i class="fa fa-clock"></i> Anggota yang Harus Segera Mengembalikan Buku</h2>
    <table style="box-shadow:0 1px 8px #0001;">
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
    <h2><i class="fa fa-exclamation-triangle"></i> Anggota yang Terlambat Mengembalikan Buku</h2>
    <table style="box-shadow:0 1px 8px #0001;">
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
</div>
</body>
</html> 