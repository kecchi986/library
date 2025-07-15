<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
$peminjaman = mysqli_query($conn, "SELECT p.*, a.nama as nama_anggota, b.judul as judul_buku FROM peminjaman p JOIN anggota a ON p.id_anggota=a.id JOIN buku b ON p.id_buku=b.id ORDER BY p.tanggal_pinjam DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Laporan Peminjaman & Pengembalian</title></head>
<body>
<h2>Laporan Peminjaman & Pengembalian</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($peminjaman)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama_anggota']) ?></td>
<td><?= htmlspecialchars($row['judul_buku']) ?></td>
<td><?= $row['tanggal_pinjam'] ?></td>
<td><?= $row['tanggal_kembali'] ?></td>
<td><?= $row['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html> 