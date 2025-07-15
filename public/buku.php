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
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $harga = $_POST['harga'];
    mysqli_query($conn, "INSERT INTO tb_buku (judul, pengarang, tahun_terbit, harga) VALUES ('$judul','$pengarang','$tahun_terbit','$harga')");
    header('Location: buku.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $harga = $_POST['harga'];
    mysqli_query($conn, "UPDATE tb_buku SET judul='$judul', pengarang='$pengarang', tahun_terbit='$tahun_terbit', harga='$harga' WHERE id=$id");
    header('Location: buku.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_buku WHERE id=$id");
    header('Location: buku.php');
    exit;
}
$buku = mysqli_query($conn, "SELECT * FROM tb_buku");
?>
<!DOCTYPE html>
<html>
<head><title>Manajemen Buku</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Manajemen Buku</h1>
<h2>Data Buku</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table>
<tr><th>No</th><th>Judul</th><th>Pengarang</th><th>Tahun Terbit</th><th>Harga</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($buku)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['judul']) ?></td>
<td><?= htmlspecialchars($row['pengarang']) ?></td>
<td><?= $row['tahun_terbit'] ?></td>
<td><?= number_format($row['harga'],0,',','.') ?></td>
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
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_buku WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
?>
Judul: <input type="text" name="judul" value="<?= $edit['judul'] ?? '' ?>" required><br>
Pengarang: <input type="text" name="pengarang" value="<?= $edit['pengarang'] ?? '' ?>" required><br>
Tahun Terbit: <input type="number" name="tahun_terbit" value="<?= $edit['tahun_terbit'] ?? '' ?>" required><br>
Harga: <input type="number" name="harga" value="<?= $edit['harga'] ?? 0 ?>" required><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</div>
</body>
</html> 