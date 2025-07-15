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
    $harga = $_POST['harga'];
    mysqli_query($conn, "INSERT INTO tb_fasilitas (nama, harga) VALUES ('$nama','$harga')");
    header('Location: fasilitas.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    mysqli_query($conn, "UPDATE tb_fasilitas SET nama='$nama', harga='$harga' WHERE id=$id");
    header('Location: fasilitas.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_fasilitas WHERE id=$id");
    header('Location: fasilitas.php');
    exit;
}
$fasilitas = mysqli_query($conn, "SELECT * FROM tb_fasilitas");
?>
<!DOCTYPE html>
<html>
<head><title>Manajemen Fasilitas</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Manajemen Fasilitas</h1>
<h2>Data Fasilitas</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table>
<tr><th>No</th><th>Nama</th><th>Harga</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($fasilitas)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td><?= number_format($row['harga'],0,',','.') ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> |
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus fasilitas?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Fasilitas</h3>
<form method="post">
<?php
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_fasilitas WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
?>
Nama: <input type="text" name="nama" value="<?= $edit['nama'] ?? '' ?>" required><br>
Harga: <input type="number" name="harga" value="<?= $edit['harga'] ?? 0 ?>" required><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</div>
</body>
</html> 