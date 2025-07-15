<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$buku = mysqli_query($conn, "SELECT * FROM tb_buku");
$anggota = mysqli_query($conn, "SELECT * FROM tb_anggota");
$peminjaman = mysqli_query($conn, "SELECT p.*, b.judul, a.nama FROM tb_peminjaman p JOIN tb_buku b ON p.id_buku=b.id JOIN tb_anggota a ON p.id_anggota=a.id");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manajemen Peminjaman</title>
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
            <a href="peminjaman.php" class="active"><i class="fa fa-arrow-right-arrow-left"></i><span>Manajemen Peminjaman</span></a>
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
        <div class="dashboard-title"><i class="fa fa-arrow-right-arrow-left"></i> Manajemen Peminjaman</div>
        <h2>Data Peminjaman</h2>
        <table>
        <tr><th>No</th><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($peminjaman)): ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= $row['tgl_pinjam'] ?></td>
        <td><?= $row['tgl_kembali'] ?></td>
        <td>
        <a href="?edit=<?= $row['id'] ?>">Edit</a> |
        <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus peminjaman?')">Hapus</a>
        </td>
        </tr>
        <?php endwhile; ?>
        </table>
        <h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Peminjaman</h3>
        <form method="post">
        <?php
        $edit = null;
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_peminjaman WHERE id=$id"));
            echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
        }
        // Reset pointer
        mysqli_data_seek($buku, 0);
        mysqli_data_seek($anggota, 0);
        ?>
        Buku: <select name="id_buku" required>
        <option value="">Pilih Buku</option>
        <?php while($b=mysqli_fetch_assoc($buku)): ?>
        <option value="<?= $b['id'] ?>" <?= isset($edit)&&$edit['id_buku']==$b['id']?'selected':'' ?>><?= htmlspecialchars($b['judul']) ?></option>
        <?php endwhile; ?>
        </select><br>
        Anggota: <select name="id_anggota" required>
        <option value="">Pilih Anggota</option>
        <?php while($a=mysqli_fetch_assoc($anggota)): ?>
        <option value="<?= $a['id'] ?>" <?= isset($edit)&&$edit['id_anggota']==$a['id']?'selected':'' ?>><?= htmlspecialchars($a['nama']) ?></option>
        <?php endwhile; ?>
        </select><br>
        Tgl Pinjam: <input type="date" name="tgl_pinjam" value="<?= $edit['tgl_pinjam'] ?? date('Y-m-d') ?>" required><br>
        Tgl Kembali: <input type="date" name="tgl_kembali" value="<?= $edit['tgl_kembali'] ?? '' ?>"><br>
        <button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
        </form>
    </main>
</div>
</body>
</html> 