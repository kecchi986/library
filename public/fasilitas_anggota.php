<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
$anggota = mysqli_query($conn, "SELECT * FROM tb_anggota");
$fasilitas = mysqli_query($conn, "SELECT * FROM tb_fasilitas");
// Proses tambah, edit, hapus
if (isset($_POST['tambah'])) {
    $id_anggota = $_POST['id_anggota'];
    $id_fasilitas = $_POST['id_fasilitas'];
    mysqli_query($conn, "INSERT INTO tb_fasilitas_anggota (id_anggota, id_fasilitas) VALUES ('$id_anggota','$id_fasilitas')");
    header('Location: fasilitas_anggota.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $id_anggota = $_POST['id_anggota'];
    $id_fasilitas = $_POST['id_fasilitas'];
    mysqli_query($conn, "UPDATE tb_fasilitas_anggota SET id_anggota='$id_anggota', id_fasilitas='$id_fasilitas' WHERE id=$id");
    header('Location: fasilitas_anggota.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_fasilitas_anggota WHERE id=$id");
    header('Location: fasilitas_anggota.php');
    exit;
}
$data = mysqli_query($conn, "SELECT f.*, a.nama as anggota, s.nama as fasilitas FROM tb_fasilitas_anggota f JOIN tb_anggota a ON f.id_anggota=a.id JOIN tb_fasilitas s ON f.id_fasilitas=s.id");
?>
<!DOCTYPE html>
<html>
<head><title>Fasilitas Anggota</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Fasilitas Anggota</h1>
<h2>Data Fasilitas Anggota</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table>
<tr><th>No</th><th>Anggota</th><th>Fasilitas</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['anggota']) ?></td>
<td><?= htmlspecialchars($row['fasilitas']) ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> |
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Fasilitas Anggota</h3>
<form method="post">
<?php
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_fasilitas_anggota WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
// Reset pointer
mysqli_data_seek($anggota, 0);
mysqli_data_seek($fasilitas, 0);
?>
Anggota: <select name="id_anggota" required>
<option value="">Pilih Anggota</option>
<?php while($a=mysqli_fetch_assoc($anggota)): ?>
<option value="<?= $a['id'] ?>" <?= isset($edit)&&$edit['id_anggota']==$a['id']?'selected':'' ?>><?= htmlspecialchars($a['nama']) ?></option>
<?php endwhile; ?>
</select><br>
Fasilitas: <select name="id_fasilitas" required>
<option value="">Pilih Fasilitas</option>
<?php while($f=mysqli_fetch_assoc($fasilitas)): ?>
<option value="<?= $f['id'] ?>" <?= isset($edit)&&$edit['id_fasilitas']==$f['id']?'selected':'' ?>><?= htmlspecialchars($f['nama']) ?></option>
<?php endwhile; ?>
</select><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</div>
</body>
</html> 