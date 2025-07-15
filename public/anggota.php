<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
// Proses tambah, edit, hapus
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    $tgl_daftar = $_POST['tgl_daftar'];
    $tgl_keluar = $_POST['tgl_keluar'] ? "'".$_POST['tgl_keluar']."'" : 'NULL';
    mysqli_query($conn, "INSERT INTO tb_anggota (nama, no_ktp, no_hp, tgl_daftar, tgl_keluar) VALUES ('$nama','$no_ktp','$no_hp','$tgl_daftar',$tgl_keluar)");
    header('Location: anggota.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    $tgl_daftar = $_POST['tgl_daftar'];
    $tgl_keluar = $_POST['tgl_keluar'] ? "'".$_POST['tgl_keluar']."'" : 'NULL';
    mysqli_query($conn, "UPDATE tb_anggota SET nama='$nama', no_ktp='$no_ktp', no_hp='$no_hp', tgl_daftar='$tgl_daftar', tgl_keluar=$tgl_keluar WHERE id=$id");
    header('Location: anggota.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_anggota WHERE id=$id");
    header('Location: anggota.php');
    exit;
}
$anggota = mysqli_query($conn, "SELECT * FROM tb_anggota");
?>
<!DOCTYPE html>
<html>
<head><title>Manajemen Anggota</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Manajemen Anggota</h1>
<h2>Data Anggota</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table>
<tr><th>No</th><th>Nama</th><th>No KTP</th><th>No HP</th><th>Tgl Daftar</th><th>Tgl Keluar</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($anggota)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td><?= htmlspecialchars($row['no_ktp']) ?></td>
<td><?= htmlspecialchars($row['no_hp']) ?></td>
<td><?= $row['tgl_daftar'] ?></td>
<td><?= $row['tgl_keluar'] ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> |
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus anggota?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Anggota</h3>
<form method="post">
<?php
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_anggota WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
?>
Nama: <input type="text" name="nama" value="<?= $edit['nama'] ?? '' ?>" required><br>
No KTP: <input type="text" name="no_ktp" value="<?= $edit['no_ktp'] ?? '' ?>" required><br>
No HP: <input type="text" name="no_hp" value="<?= $edit['no_hp'] ?? '' ?>" required><br>
Tgl Daftar: <input type="date" name="tgl_daftar" value="<?= $edit['tgl_daftar'] ?? date('Y-m-d') ?>" required><br>
Tgl Keluar: <input type="date" name="tgl_keluar" value="<?= $edit['tgl_keluar'] ?? '' ?>"><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</div>
</body>
</html> 