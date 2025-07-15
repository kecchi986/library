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
$user = $_SESSION['user'];
$fasilitas = mysqli_query($conn, "SELECT * FROM tb_fasilitas");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manajemen Fasilitas</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">Perpustakaan</div>
        <nav>
            <a href="dashboard.php"><i class="fa fa-home"></i><span>Dashboard</span></a>
            <a href="anggota.php"><i class="fa fa-users"></i><span>Manajemen Anggota</span></a>
            <a href="buku.php"><i class="fa fa-book"></i><span>Manajemen Buku</span></a>
            <a href="fasilitas.php" class="active"><i class="fa fa-cube"></i><span>Manajemen Fasilitas</span></a>
            <a href="peminjaman.php"><i class="fa fa-arrow-right-arrow-left"></i><span>Manajemen Peminjaman</span></a>
            <a href="fasilitas_anggota.php"><i class="fa fa-box"></i><span>Fasilitas Anggota</span></a>
            <a href="tagihan.php"><i class="fa fa-file-invoice-dollar"></i><span>Tagihan</span></a>
            <a href="pembayaran.php"><i class="fa fa-money-bill"></i><span>Pembayaran</span></a>
        </nav>
        <div class="user">
            <div style="font-weight:600; margin-bottom:2px;"><i class="fa fa-user-circle"></i> <?= htmlspecialchars($user['nama']) ?></div>
            <div style="font-size:13px; opacity:.8;">(<?= $user['role'] ?>)</div>
        </div>
        <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </aside>
    <main class="main">
        <div class="dashboard-title"><i class="fa fa-cube"></i> Manajemen Fasilitas</div>
        <h2>Data Fasilitas</h2>
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
    </main>
</div>
</body>
</html> 