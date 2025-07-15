<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'anggota') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$anggota = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anggota WHERE nama='".$user['nama']."' LIMIT 1"));
$id_anggota = $anggota ? $anggota['id'] : 0;
$peminjaman = mysqli_query($conn, "SELECT p.*, b.judul as judul_buku FROM peminjaman p JOIN buku b ON p.id_buku=b.id WHERE p.id_anggota=$id_anggota ORDER BY p.tanggal_pinjam DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Riwayat Peminjaman</title></head>
<body>
<h2>Riwayat Peminjaman</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($peminjaman)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['judul_buku']) ?></td>
<td><?= $row['tanggal_pinjam'] ?></td>
<td><?= $row['tanggal_kembali'] ?></td>
<td><?= $row['status'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html> 