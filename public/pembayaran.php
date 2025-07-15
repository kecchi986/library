<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$tagihan = mysqli_query($conn, "SELECT t.id, a.nama, t.bulan FROM tb_tagihan t JOIN tb_peminjaman p ON t.id_peminjaman=p.id JOIN tb_anggota a ON p.id_anggota=a.id");
$pembayaran = mysqli_query($conn, "SELECT p.*, a.nama, t.bulan FROM tb_pembayaran p JOIN tb_tagihan t ON p.id_tagihan=t.id JOIN tb_peminjaman pm ON t.id_peminjaman=pm.id JOIN tb_anggota a ON pm.id_anggota=a.id");
?>
<!DOCTYPE html>
<html>
<head>
<title>Data Pembayaran</title>
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
            <a href="tagihan.php"><i class="fa fa-file-invoice-dollar"></i><span>Tagihan</span></a>
            <a href="pembayaran.php" class="active"><i class="fa fa-money-bill"></i><span>Pembayaran</span></a>
        </nav>
        <div class="user">
            <div style="font-weight:600; margin-bottom:2px;"><i class="fa fa-user-circle"></i> <?= htmlspecialchars($user['nama']) ?></div>
            <div style="font-size:13px; opacity:.8;">(<?= $user['role'] ?>)</div>
        </div>
        <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </aside>
    <main class="main">
        <div class="dashboard-title"><i class="fa fa-money-bill"></i> Data Pembayaran</div>
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
    </main>
</div>
</body>
</html> 