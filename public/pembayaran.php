<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
$tagihan = mysqli_query($conn, "SELECT t.id, a.nama, t.bulan FROM tb_tagihan t JOIN tb_peminjaman p ON t.id_peminjaman=p.id JOIN tb_anggota a ON p.id_anggota=a.id");
// Proses tambah, edit, hapus
if (isset($_POST['tambah'])) {
    $id_tagihan = $_POST['id_tagihan'];
    $jml_bayar = $_POST['jml_bayar'];
    $status = $_POST['status'];
    mysqli_query($conn, "INSERT INTO tb_pembayaran (id_tagihan, jml_bayar, status) VALUES ('$id_tagihan','$jml_bayar','$status')");
    header('Location: pembayaran.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $id_tagihan = $_POST['id_tagihan'];
    $jml_bayar = $_POST['jml_bayar'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE tb_pembayaran SET id_tagihan='$id_tagihan', jml_bayar='$jml_bayar', status='$status' WHERE id=$id");
    header('Location: pembayaran.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM tb_pembayaran WHERE id=$id");
    header('Location: pembayaran.php');
    exit;
}
$pembayaran = mysqli_query($conn, "SELECT p.*, a.nama, t.bulan FROM tb_pembayaran p JOIN tb_tagihan t ON p.id_tagihan=t.id JOIN tb_peminjaman pm ON t.id_peminjaman=pm.id JOIN tb_anggota a ON pm.id_anggota=a.id");
?>
<!DOCTYPE html>
<html>
<head><title>Data Pembayaran</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Data Pembayaran</h1>
<a href="dashboard.php">Kembali ke Dashboard</a>
<table>
<tr><th>No</th><th>Anggota</th><th>Bulan</th><th>Jumlah Bayar</th><th>Status</th><th>Aksi</th></tr>
<?php $no=1; while($row=mysqli_fetch_assoc($pembayaran)): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($row['nama']) ?></td>
<td><?= $row['bulan'] ?></td>
<td><?= number_format($row['jml_bayar'],0,',','.') ?></td>
<td><?= $row['status'] ?></td>
<td>
<a href="?edit=<?= $row['id'] ?>">Edit</a> |
<a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus pembayaran?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Pembayaran</h3>
<form method="post">
<?php
$edit = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_pembayaran WHERE id=$id"));
    echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
}
// Reset pointer
mysqli_data_seek($tagihan, 0);
?>
Tagihan: <select name="id_tagihan" required>
<option value="">Pilih Tagihan</option>
<?php while($t=mysqli_fetch_assoc($tagihan)): ?>
<option value="<?= $t['id'] ?>" <?= isset($edit)&&$edit['id_tagihan']==$t['id']?'selected':'' ?>><?= htmlspecialchars($t['nama']).' - '.$t['bulan'] ?></option>
<?php endwhile; ?>
</select><br>
Jumlah Bayar: <input type="number" name="jml_bayar" value="<?= $edit['jml_bayar'] ?? 0 ?>" required><br>
Status: <select name="status" required>
<option value="lunas" <?= isset($edit)&&$edit['status']=='lunas'?'selected':'' ?>>Lunas</option>
<option value="cicil" <?= isset($edit)&&$edit['status']=='cicil'?'selected':'' ?>>Cicil</option>
</select><br>
<button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
</form>
</div>
</body>
</html> 