<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
// Proses tambah, edit, hapus
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $nama = $_POST['nama'];
    mysqli_query($conn, "INSERT INTO users (username, password, nama, role) VALUES ('$username','$password','$nama','petugas')");
    header('Location: petugas.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $setpass = $_POST['password'] ? ", password='".md5($_POST['password'])."'" : '';
    mysqli_query($conn, "UPDATE users SET username='$username', nama='$nama' $setpass WHERE id=$id AND role='petugas'");
    header('Location: petugas.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role='petugas'");
    header('Location: petugas.php');
    exit;
}
$petugas = mysqli_query($conn, "SELECT * FROM users WHERE role='petugas'");
?>
<!DOCTYPE html>
<html>
<head><title>Manajemen Petugas</title></head>
<body>
<h2>Data Petugas</h2>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table border="1" cellpadding="5">
<tr><th>No</th><th>Username</th><th>Nama</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($petugas)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['username']) ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> |
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus petugas?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Petugas</h3>
<form method="post">
<?php
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$id AND role='petugas'"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
?>
Username: <input type="text" name="username" value="<?= $edit['username'] ?? '' ?>" required><br>
Password: <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"><br>
Nama: <input type="text" name="nama" value="<?= $edit['nama'] ?? '' ?>" required><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</body>
</html> 