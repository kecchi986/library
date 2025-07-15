<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'petugas') {
    header('Location: login.php');
    exit;
}
// Proses peminjaman
if (isset($_POST['pinjam'])) {
    $id_anggota = $_POST['id_anggota'];
    $id_buku = $_POST['id_buku'];
    $tanggal = date('Y-m-d');
    mysqli_query($conn, "INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, status) VALUES ('$id_anggota','$id_buku','$tanggal','dipinjam')");
    mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id=$id_buku");
    header('Location: transaksi.php');
    exit;
}
// Proses pengembalian
if (isset($_GET['kembali'])) {
    $id = $_GET['kembali'];
    $tanggal = date('Y-m-d');
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_buku FROM peminjaman WHERE id=$id"));
    mysqli_query($conn, "UPDATE peminjaman SET status='kembali', tanggal_kembali='$tanggal' WHERE id=$id");
    mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id=".$row['id_buku']);
    header('Location: transaksi.php');
    exit;
}
$peminjaman = mysqli_query($conn, "SELECT p.*, a.nama as nama_anggota, b.judul as judul_buku FROM peminjaman p JOIN anggota a ON p.id_anggota=a.id JOIN buku b ON p.id_buku=b.id ORDER BY p.status, p.tanggal_pinjam DESC");
$anggota = mysqli_query($conn, "SELECT * FROM anggota");
$buku = mysqli_query($conn, "SELECT * FROM buku WHERE stok > 0");
?>
<!DOCTYPE html>
<html>
<head><title>Transaksi Peminjaman/Pengembalian</title></head>
<body>
<h2>Transaksi Peminjaman/Pengembalian</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<h3>Peminjaman Baru</h3>
<form method="post">
Anggota: <select name="id_anggota" required>
<option value="">Pilih</option>
<?php while($a=mysqli_fetch_assoc($anggota)): ?>
<option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['nama']) ?></option>
<?php endwhile; ?>
</select>
Buku: <select name="id_buku" required>
<option value="">Pilih</option>
<?php while($b=mysqli_fetch_assoc($buku)): ?>
<option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['judul']) ?></option>
<?php endwhile; ?>
</select>
<button type="submit" name="pinjam">Pinjam</button>
</form>
<h3>Daftar Peminjaman</h3>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($peminjaman)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama_anggota']) ?></td>
<td><?= htmlspecialchars($row['judul_buku']) ?></td>
<td><?= $row['tanggal_pinjam'] ?></td>
<td><?= $row['tanggal_kembali'] ?></td>
<td><?= $row['status'] ?></td>
<td>
<?php if ($row['status']=='dipinjam'): ?>
<a href="?kembali=<?= $row['id'] ?>" onclick="return confirm('Konfirmasi pengembalian?')">Kembalikan</a>
<?php else: ?>-
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html> 