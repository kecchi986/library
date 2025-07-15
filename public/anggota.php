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
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    $tgl_daftar = $_POST['tgl_daftar'];
    $tgl_keluar = $_POST['tgl_keluar'] ? "'".$_POST['tgl_keluar']."'" : 'NULL';
    mysqli_query($conn, "INSERT INTO tb_anggota (nama, no_ktp, no_hp, tgl_daftar, tgl_keluar) VALUES ('$nama','$no_ktp','$no_hp','$tgl_daftar',$tgl_keluar)");
    header('Location: anggota.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    $tgl_daftar = $_POST['tgl_daftar'];
    $tgl_keluar = $_POST['tgl_keluar'] ? "'".$_POST['tgl_keluar']."'" : 'NULL';
    mysqli_query($conn, "UPDATE tb_anggota SET nama='$nama', no_ktp='$no_ktp', no_hp='$no_hp', tgl_daftar='$tgl_daftar', tgl_keluar=$tgl_keluar WHERE id=$id");
    header('Location: anggota.php');
    exit;
}
$alert = '';
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $force = isset($_GET['force']) ? true : false;
    // Cek relasi di fasilitas_anggota dan peminjaman
    $cek_fasilitas = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_fasilitas_anggota WHERE id_anggota=$id"));
    $cek_peminjaman = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM tb_peminjaman WHERE id_anggota=$id"));
    if (($cek_fasilitas > 0 || $cek_peminjaman > 0) && !$force) {
        $alert = "<div class='alert' style='background:#ffe0e0;color:#b00;padding:18px 20px;margin:18px 0;border-radius:8px;box-shadow:0 2px 8px #fbb;'>";
        $alert .= "<div style='font-size:22px;font-weight:bold;display:flex;align-items:center;gap:10px;'><i class='fa fa-exclamation-triangle' style='color:#e67e22;'></i> PERINGATAN!</div>";
        $alert .= "<div style='margin:10px 0 8px 0;font-size:16px;'>Anggota ini masih memiliki data terkait di sistem:</div>";
        $alert .= "<ul style='margin:0 0 10px 20px;font-size:15px;'>";
        if($cek_fasilitas>0) $alert .= "<li><b>$cek_fasilitas</b> fasilitas anggota</li>";
        if($cek_peminjaman>0) $alert .= "<li><b>$cek_peminjaman</b> data peminjaman</li>";
        $alert .= "</ul>";
        $alert .= "<div style='margin-bottom:10px;'>Menghapus anggota ini akan <b>menghapus seluruh data terkait</b> secara permanen. Lanjutkan?</div>";
        $alert .= "<div style='display:flex;gap:10px;'>";
        $alert .= "<a href='?hapus=$id&force=1' style='color:#fff;background:#b00;padding:7px 18px;border-radius:4px;text-decoration:none;font-weight:bold;box-shadow:0 1px 4px #d88;'>Ya, hapus semua</a>";
        $alert .= "<a href='anggota.php' style='color:#333;background:#eee;padding:7px 18px;border-radius:4px;text-decoration:none;font-weight:bold;border:1px solid #ccc;'>Batal</a>";
        $alert .= "</div>";
        $alert .= "</div>";
    } else {
        // Hapus data relasi jika force
        if($force) {
            // Hapus pembayaran dan tagihan terkait peminjaman anggota
            $peminjaman_ids = [];
            $res = mysqli_query($conn, "SELECT id FROM tb_peminjaman WHERE id_anggota=$id");
            while($row = mysqli_fetch_assoc($res)) $peminjaman_ids[] = $row['id'];
            if(count($peminjaman_ids)) {
                $ids = implode(',', $peminjaman_ids);
                $tagihan_ids = [];
                $res2 = mysqli_query($conn, "SELECT id FROM tb_tagihan WHERE id_peminjaman IN ($ids)");
                while($row2 = mysqli_fetch_assoc($res2)) $tagihan_ids[] = $row2['id'];
                if(count($tagihan_ids)) {
                    $ids2 = implode(',', $tagihan_ids);
                    mysqli_query($conn, "DELETE FROM tb_pembayaran WHERE id_tagihan IN ($ids2)");
                    mysqli_query($conn, "DELETE FROM tb_tagihan WHERE id IN ($ids2)");
                }
                mysqli_query($conn, "DELETE FROM tb_peminjaman WHERE id IN ($ids)");
            }
            mysqli_query($conn, "DELETE FROM tb_fasilitas_anggota WHERE id_anggota=$id");
        }
        mysqli_query($conn, "DELETE FROM tb_anggota WHERE id=$id");
        header('Location: anggota.php');
        exit;
    }
}
$user = $_SESSION['user'];
$anggota = mysqli_query($conn, "SELECT * FROM tb_anggota");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manajemen Anggota</title>
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
            <a href="anggota.php" class="active"><i class="fa fa-users"></i><span>Manajemen Anggota</span></a>
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
        <?php if($alert) echo $alert; ?>
        <div class="dashboard-title"><i class="fa fa-users"></i> Manajemen Anggota</div>
        <h2>Data Anggota</h2>
        <table>
        <tr><th>No</th><th>Nama</th><th>No KTP</th><th>No HP</th><th>Tgl Daftar</th><th>Tgl Keluar</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($anggota)): ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['no_ktp']) ?></td>
        <td><?= htmlspecialchars($row['no_hp']) ?></td>
        <td><?= $row['tgl_daftar'] ?></td>
        <td><?= $row['tgl_keluar'] ?></td>
        <td>
        <a href="?edit=<?= $row['id'] ?>">Edit</a> |
        <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus anggota?')">Hapus</a>
        </td>
        </tr>
        <?php endwhile; ?>
        </table>
        <h3><?= isset($_GET['edit']) ? 'Edit' : 'Tambah' ?> Anggota</h3>
        <form method="post">
        <?php
        $edit = null;
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_anggota WHERE id=$id"));
            echo '<input type="hidden" name="id" value="'.$edit['id'].'">';
        }
        ?>
        Nama: <input type="text" name="nama" value="<?= $edit['nama'] ?? '' ?>" required><br>
        No KTP: <input type="text" name="no_ktp" value="<?= $edit['no_ktp'] ?? '' ?>" required><br>
        No HP: <input type="text" name="no_hp" value="<?= $edit['no_hp'] ?? '' ?>" required><br>
        Tgl Daftar: <input type="date" name="tgl_daftar" value="<?= $edit['tgl_daftar'] ?? date('Y-m-d') ?>" required><br>
        Tgl Keluar: <input type="date" name="tgl_keluar" value="<?= $edit['tgl_keluar'] ?? '' ?>"><br>
        <button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
        </form>
    </main>
</div>
</body>
</html> 