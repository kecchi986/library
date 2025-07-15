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
$alert = '';
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $force = isset($_GET['force']) ? true : false;
    $cek_relasi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_fasilitas_anggota WHERE id_fasilitas=$id"));
    if ($cek_relasi > 0 && !$force) {
        $alert = "<div class='alert' style='background:#ffe0e0;color:#b00;padding:18px 20px;margin:18px 0;border-radius:8px;box-shadow:0 2px 8px #fbb;'>";
        $alert .= "<div style='font-size:22px;font-weight:bold;display:flex;align-items:center;gap:10px;'><i class='fa fa-exclamation-triangle' style='color:#e67e22;'></i> PERINGATAN!</div>";
        $alert .= "<div style='margin:10px 0 8px 0;font-size:16px;'>Fasilitas ini masih digunakan oleh <b>$cek_relasi</b> anggota.</div>";
        $alert .= "<div style='margin-bottom:10px;'>Menghapus fasilitas ini akan <b>menghapus seluruh data terkait</b> secara permanen. Lanjutkan?</div>";
        $alert .= "<div style='display:flex;gap:10px;'>";
        $alert .= "<a href='?hapus=$id&force=1' style='color:#fff;background:#b00;padding:7px 18px;border-radius:4px;text-decoration:none;font-weight:bold;box-shadow:0 1px 4px #d88;'>Ya, hapus semua</a>";
        $alert .= "<a href='fasilitas.php' style='color:#333;background:#eee;padding:7px 18px;border-radius:4px;text-decoration:none;font-weight:bold;border:1px solid #ccc;'>Batal</a>";
        $alert .= "</div>";
        $alert .= "</div>";
    } else {
        if($force) {
            mysqli_query($conn, "DELETE FROM tb_fasilitas_anggota WHERE id_fasilitas=$id");
        }
        mysqli_query($conn, "DELETE FROM tb_fasilitas WHERE id=$id");
        header('Location: fasilitas.php');
        exit;
    }
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
        <?php if($alert) echo $alert; ?>
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