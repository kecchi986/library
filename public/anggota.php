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
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    mysqli_query($conn, "INSERT INTO anggota (nama, alamat, telepon) VALUES ('$nama','$alamat','$telepon')");
    header('Location: anggota.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    mysqli_query($conn, "UPDATE anggota SET nama='$nama', alamat='$alamat', telepon='$telepon' WHERE id=$id");
    header('Location: anggota.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM anggota WHERE id=$id");
    header('Location: anggota.php');
    exit;
}
$anggota = mysqli_query($conn, "SELECT * FROM anggota");
?>
<!DOCTYPE html>
<html>
<head><title>Manajemen Anggota</title></head>
<body>
<h2>Data Anggota</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Nama</th><th>Alamat</th><th>Telepon</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($anggota)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td><?= htmlspecialchars($row['alamat']) ?></td>
<td><?= htmlspecialchars($row['telepon']) ?></td>
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
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM anggota WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
?>
Nama: <input type="text" name="nama" value="<?= $edit['nama'] ?? '' ?>" required><br>
Alamat: <input type="text" name="alamat" value="<?= $edit['alamat'] ?? '' ?>"><br>
Telepon: <input type="text" name="telepon" value="<?= $edit['telepon'] ?? '' ?>"><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</body>
</html> 