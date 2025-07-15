<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$role = $user['role'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Perpustakaan</title>
</head>
<body>
    <h2>Selamat datang, <?php echo htmlspecialchars($user['nama']); ?> (<?php echo $role; ?>)</h2>
    <ul>
        <?php if ($role == 'admin'): ?>
            <li><a href="buku.php">Manajemen Buku</a></li>
            <li><a href="anggota.php">Manajemen Anggota</a></li>
            <li><a href="petugas.php">Manajemen Petugas</a></li>
            <li><a href="laporan.php">Laporan</a></li>
        <?php elseif ($role == 'petugas'): ?>
            <li><a href="transaksi.php">Transaksi Peminjaman/Pengembalian</a></li>
            <li><a href="anggota.php">Data Anggota</a></li>
            <li><a href="buku.php">Data Buku</a></li>
        <?php else: ?>
            <li><a href="katalog.php">Katalog Buku</a></li>
            <li><a href="riwayat.php">Riwayat Peminjaman</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html> 