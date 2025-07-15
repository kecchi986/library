<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
function countTable($conn, $table) {
    $r = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM $table"));
    return $r[0];
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">Perpustakaan</div>
        <nav>
            <a href="dashboard.php" class="active"><i class="fa fa-home"></i><span>Dashboard</span></a>
            <a href="anggota.php"><i class="fa fa-users"></i><span>Manajemen Anggota</span></a>
            <a href="buku.php"><i class="fa fa-book"></i><span>Manajemen Buku</span></a>
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
        <div class="dashboard-title"><i class="fa fa-gauge"></i> Dashboard</div>
        <div class="dashboard-subtitle">Selamat datang di sistem perpustakaan</div>
        <div class="card-row">
            <div class="card blue"><span class="icon"><i class="fa fa-users"></i></span><div><div><?= countTable($conn,'tb_anggota') ?></div><div style="font-size:14px;font-weight:400;">Total Anggota</div></div></div>
            <div class="card green"><span class="icon"><i class="fa fa-book"></i></span><div><div><?= countTable($conn,'tb_buku') ?></div><div style="font-size:14px;font-weight:400;">Total Buku</div></div></div>
            <div class="card yellow"><span class="icon"><i class="fa fa-cube"></i></span><div><div><?= countTable($conn,'tb_fasilitas') ?></div><div style="font-size:14px;font-weight:400;">Total Fasilitas</div></div></div>
            <div class="card purple"><span class="icon"><i class="fa fa-arrow-right-arrow-left"></i></span><div><div><?= countTable($conn,'tb_peminjaman') ?></div><div style="font-size:14px;font-weight:400;">Total Peminjaman</div></div></div>
        </div>
        <div class="dashboard-title" style="font-size:1.3rem;margin-bottom:18px;"><i class="fa fa-bolt"></i> Aksi Cepat</div>
        <div class="quick-actions">
            <a href="peminjaman.php" class="quick-action"><i class="fa fa-plus"></i>Tambah Peminjaman</a>
            <a href="anggota.php" class="quick-action"><i class="fa fa-users"></i>Kelola Anggota</a>
            <a href="buku.php" class="quick-action"><i class="fa fa-book"></i>Kelola Buku</a>
            <a href="fasilitas.php" class="quick-action"><i class="fa fa-cube"></i>Kelola Fasilitas</a>
        </div>
    </main>
</div>
</body>
</html> 