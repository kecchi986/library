<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
if (isset($_POST['generate'])) {
    $bulan = date('Y-m');
    $peminjaman = mysqli_query($conn, "SELECT p.*, a.id as id_anggota FROM tb_peminjaman p JOIN tb_anggota a ON p.id_anggota=a.id WHERE DATE_FORMAT(p.tgl_pinjam,'%Y-%m')='$bulan' AND p.id NOT IN (SELECT id_peminjaman FROM tb_tagihan WHERE bulan='$bulan')");
    while($row=mysqli_fetch_assoc($peminjaman)) {
        $id_peminjaman = $row['id'];
        $id_anggota = $row['id_anggota'];
        $buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga FROM tb_buku WHERE id=".$row['id_buku']));
        $harga_buku = $buku ? $buku['harga'] : 0;
        $q = mysqli_query($conn, "SELECT SUM(f.harga) as total FROM tb_fasilitas_anggota fa JOIN tb_fasilitas f ON fa.id_fasilitas=f.id WHERE fa.id_anggota=$id_anggota");
        $fasilitas = mysqli_fetch_assoc($q);
        $harga_fasilitas = $fasilitas['total'] ?? 0;
        $jml_tagihan = $harga_buku + $harga_fasilitas;
        mysqli_query($conn, "INSERT INTO tb_tagihan (bulan, id_peminjaman, jml_tagihan) VALUES ('$bulan','$id_peminjaman','$jml_tagihan')");
    }
    header('Location: tagihan.php');
    exit;
}
$tagihan = mysqli_query($conn, "SELECT t.*, a.nama, b.judul FROM tb_tagihan t JOIN tb_peminjaman p ON t.id_peminjaman=p.id JOIN tb_anggota a ON p.id_anggota=a.id JOIN tb_buku b ON p.id_buku=b.id ORDER BY t.bulan DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Tagihan Bulanan</title>
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
            <a href="fasilitas.php"><i class="fa fa-cube"></i><span>Manajemen Fasilitas</span></a>
            <a href="peminjaman.php"><i class="fa fa-arrow-right-arrow-left"></i><span>Manajemen Peminjaman</span></a>
            <a href="fasilitas_anggota.php"><i class="fa fa-box"></i><span>Fasilitas Anggota</span></a>
            <a href="tagihan.php" class="active"><i class="fa fa-file-invoice-dollar"></i><span>Tagihan</span></a>
            <a href="pembayaran.php"><i class="fa fa-money-bill"></i><span>Pembayaran</span></a>
        </nav>
        <div class="user">
            <div style="font-weight:600; margin-bottom:2px;"><i class="fa fa-user-circle"></i> <?= htmlspecialchars($user['nama']) ?></div>
            <div style="font-size:13px; opacity:.8;">(<?= $user['role'] ?>)</div>
        </div>
        <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </aside>
    <main class="main">
        <div class="dashboard-title"><i class="fa fa-file-invoice-dollar"></i> Tagihan Bulanan</div>
        <form method="post" style="margin:10px 0;">
            <button type="submit" name="generate" onclick="return confirm('Generate tagihan bulan ini?')">Generate Tagihan Bulan Ini</button>
        </form>
        <table>
        <tr><th>No</th><th>Bulan</th><th>Anggota</th><th>Buku</th><th>Jumlah Tagihan</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($tagihan)): ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['bulan'] ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= number_format($row['jml_tagihan'],0,',','.') ?></td>
        </tr>
        <?php endwhile; ?>
        </table>
    </main>
</div>
</body>
</html> 