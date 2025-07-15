<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$buku = mysqli_query($conn, "SELECT * FROM tb_buku");

if (isset($_POST['tambah'])) {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    mysqli_query($conn, "INSERT INTO tb_buku (judul, pengarang, tahun_terbit, harga, stok) VALUES ('$judul','$pengarang','$tahun_terbit','$harga','$stok')");
    header('Location: buku.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    mysqli_query($conn, "UPDATE tb_buku SET judul='$judul', pengarang='$pengarang', tahun_terbit='$tahun_terbit', harga='$harga', stok='$stok' WHERE id=$id");
    header('Location: buku.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Manajemen Buku</title>
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
            <a href="buku.php" class="active"><i class="fa fa-book"></i><span>Manajemen Buku</span></a>
            <a href="fasilitas.php"><i class="fa fa-cube"></i><span>Manajemen Fasilitas</span></a>
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
        <div class="dashboard-title"><i class="fa fa-book"></i> Manajemen Buku</div>
        <h2>Data Buku</h2>
        <table>
        <tr><th>No</th><th>Judul</th><th>Pengarang</th><th>Tahun Terbit</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($buku)): ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= htmlspecialchars($row['pengarang']) ?></td>
        <td><?= $row['tahun_terbit'] ?></td>
        <td><?= number_format($row['harga'],0,',','.') ?></td>
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
            $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_buku WHERE id=$id"));
            echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
        }
        ?>
        Judul: <input type="text" name="judul" value="<?= $edit['judul'] ?? '' ?>" required><br>
        Pengarang: <input type="text" name="pengarang" value="<?= $edit['pengarang'] ?? '' ?>" required><br>
        Tahun Terbit: <input type="number" name="tahun_terbit" value="<?= $edit['tahun_terbit'] ?? '' ?>" required><br>
        Harga: <input type="number" name="harga" value="<?= $edit['harga'] ?? 0 ?>" required><br>
        Stok: <input type="number" name="stok" value="<?= $edit['stok'] ?? 0 ?>" required><br>
        <button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
        </form>
    </main>
</div>
</body>
</html> 