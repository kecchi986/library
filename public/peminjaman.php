<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
// Ambil data buku dan anggota
$buku = mysqli_query($conn, "SELECT * FROM tb_buku");
$anggota = mysqli_query($conn, "SELECT * FROM tb_anggota");
// Proses tambah, edit, hapus
if (isset($_POST['tambah'])) {
    $id_buku = $_POST['id_buku'];
    $id_anggota = $_POST['id_anggota'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'] ? "'".$_POST['tgl_kembali']."'" : 'NULL';
    mysqli_query($conn, "INSERT INTO tb_peminjaman (id_buku, id_anggota, tgl_pinjam, tgl_kembali) VALUES ('$id_buku','$id_anggota','$tgl_pinjam',$tgl_kembali)");
    header('Location: peminjaman.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $id_buku = $_POST['id_buku'];
    $id_anggota = $_POST['id_anggota'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'] ? "'".$_POST['tgl_kembali']."'" : 'NULL';
    mysqli_query($conn, "UPDATE tb_peminjaman SET id_buku='$id_buku', id_anggota='$id_anggota', tgl_pinjam='$tgl_pinjam', tgl_kembali=$tgl_kembali WHERE id=$id");
    header('Location: peminjaman.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_peminjaman WHERE id=$id");
    header('Location: peminjaman.php');
    exit;
}
$peminjaman = mysqli_query($conn, "SELECT p.*, b.judul, a.nama FROM tb_peminjaman p JOIN tb_buku b ON p.id_buku=b.id JOIN tb_anggota a ON p.id_anggota=a.id");
?>
<!DOCTYPE html>
<html>
<head><title>Manajemen Peminjaman</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Manajemen Peminjaman</h1>
<h2>Data Peminjaman</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table>
<tr><th>No</th><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($peminjaman)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['judul']) ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td><?= $row['tgl_pinjam'] ?></td>
<td><?= $row['tgl_kembali'] ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> |
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus peminjaman?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Peminjaman</h3>
<form method="post">
<?php
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_peminjaman WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
// Reset pointer
mysqli_data_seek($buku, 0);
mysqli_data_seek($anggota, 0);
?>
Buku: <select name="id_buku" required>
<option value="">Pilih Buku</option>
<?php while($b=mysqli_fetch_assoc($buku)): ?>
<option value="<?= $b['id'] ?>" <?= isset($edit)&&$edit['id_buku']==$b['id']?'selected':'' ?>><?= htmlspecialchars($b['judul']) ?></option>
<?php endwhile; ?>
</select><br>
Anggota: <select name="id_anggota" required>
<option value="">Pilih Anggota</option>
<?php while($a=mysqli_fetch_assoc($anggota)): ?>
<option value="<?= $a['id'] ?>" <?= isset($edit)&&$edit['id_anggota']==$a['id']?'selected':'' ?>><?= htmlspecialchars($a['nama']) ?></option>
<?php endwhile; ?>
</select><br>
Tgl Pinjam: <input type="date" name="tgl_pinjam" value="<?= $edit['tgl_pinjam'] ?? date('Y-m-d') ?>" required><br>
Tgl Kembali: <input type="date" name="tgl_kembali" value="<?= $edit['tgl_kembali'] ?? '' ?>"><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</div>
</body>
</html> 