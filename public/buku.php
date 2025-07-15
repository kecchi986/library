<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
// Proses tambah, edit, hapus
if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    mysqli_query($conn, "INSERT INTO buku (judul, penulis, penerbit, tahun, stok) VALUES ('$judul','$penulis','$penerbit','$tahun','$stok')");
    header('Location: buku.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];
    $stok = $_POST['stok'];
    mysqli_query($conn, "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', tahun='$tahun', stok='$stok' WHERE id=$id");
    header('Location: buku.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM buku WHERE id=$id");
    header('Location: buku.php');
    exit;
}
$buku = mysqli_query($conn, "SELECT * FROM buku");
?>
<!DOCTYPE html>
<html>
<head><title>Manajemen Buku</title></head>
<body>
<h2>Data Buku</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Judul</th><th>Penulis</th><th>Penerbit</th><th>Tahun</th><th>Stok</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($buku)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['judul']) ?></td>
<td><?= htmlspecialchars($row['penulis']) ?></td>
<td><?= htmlspecialchars($row['penerbit']) ?></td>
<td><?= $row['tahun'] ?></td>
<td><?= $row['stok'] ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> |
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus buku?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Buku</h3>
<form method="post">
<?php
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
?>
Judul: <input type="text" name="judul" value="<?= $edit['judul'] ?? '' ?>" required><br>
Penulis: <input type="text" name="penulis" value="<?= $edit['penulis'] ?? '' ?>"><br>
Penerbit: <input type="text" name="penerbit" value="<?= $edit['penerbit'] ?? '' ?>"><br>
Tahun: <input type="number" name="tahun" value="<?= $edit['tahun'] ?? '' ?>"><br>
Stok: <input type="number" name="stok" value="<?= $edit['stok'] ?? 0 ?>"><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</body>
</html> 