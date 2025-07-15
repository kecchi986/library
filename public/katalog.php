<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'anggota') {
    header('Location: login.php');
    exit;
}
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$where = $cari ? "WHERE judul LIKE '%$cari%' OR penulis LIKE '%$cari%'" : '';
$buku = mysqli_query($conn, "SELECT * FROM buku $where");
?>
<!DOCTYPE html>
<html>
<head><title>Katalog Buku</title></head>
<body>
<h2>Katalog Buku</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<form method="get">
    <input type="text" name="cari" placeholder="Cari judul/penulis" value="<?= htmlspecialchars($cari) ?>">
    <button type="submit">Cari</button>
</form>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Judul</th><th>Penulis</th><th>Penerbit</th><th>Tahun</th><th>Stok</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($buku)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['judul']) ?></td>
<td><?= htmlspecialchars($row['penulis']) ?></td>
<td><?= htmlspecialchars($row['penerbit']) ?></td>
<td><?= $row['tahun'] ?></td>
<td><?= $row['stok'] ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html> 