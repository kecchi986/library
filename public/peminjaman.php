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
// Tambahan: query buku masih dipinjam dan sudah dikembalikan
$masih = mysqli_query($conn, "SELECT p.*, b.judul, a.nama FROM tb_peminjaman p JOIN tb_buku b ON p.id_buku=b.id JOIN tb_anggota a ON p.id_anggota=a.id WHERE p.tgl_kembali IS NULL");
$sudah = mysqli_query($conn, "SELECT p.*, b.judul, a.nama FROM tb_peminjaman p JOIN tb_buku b ON p.id_buku=b.id JOIN tb_anggota a ON p.id_anggota=a.id WHERE p.tgl_kembali IS NOT NULL");

// Proses tambah/edit/hapus peminjaman
if (isset($_POST['tambah'])) {
    $id_buku = $_POST['id_buku'];
    $id_anggota = $_POST['id_anggota'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'] ? "'".$_POST['tgl_kembali']."'" : 'NULL';
    mysqli_query($conn, "INSERT INTO tb_peminjaman (id_buku, id_anggota, tgl_pinjam, tgl_kembali) VALUES ($id_buku, $id_anggota, '$tgl_pinjam', $tgl_kembali)");
    // Kurangi stok buku
    mysqli_query($conn, "UPDATE tb_buku SET stok = stok - 1 WHERE id=$id_buku");
    header('Location: peminjaman.php');
    exit;
}
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $id_buku = $_POST['id_buku'];
    $id_anggota = $_POST['id_anggota'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'] ? "'".$_POST['tgl_kembali']."'" : 'NULL';
    // Cek status sebelumnya
    $prev = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tgl_kembali FROM tb_peminjaman WHERE id=$id"));
    $was_returned = $prev && $prev['tgl_kembali'] ? true : false;
    $will_returned = $_POST['tgl_kembali'] ? true : false;
    mysqli_query($conn, "UPDATE tb_peminjaman SET id_buku=$id_buku, id_anggota=$id_anggota, tgl_pinjam='$tgl_pinjam', tgl_kembali=$tgl_kembali WHERE id=$id");
    // Jika sebelumnya belum dikembalikan, sekarang dikembalikan, tambah stok
    if (!$was_returned && $will_returned) {
        mysqli_query($conn, "UPDATE tb_buku SET stok = stok + 1 WHERE id=$id_buku");
    }
    header('Location: peminjaman.php');
    exit;
}
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_buku, tgl_kembali FROM tb_peminjaman WHERE id=$id"));
    // Jika belum dikembalikan, kembalikan stok
    if ($row && !$row['tgl_kembali']) {
        mysqli_query($conn, "UPDATE tb_buku SET stok = stok + 1 WHERE id=".$row['id_buku']);
    }
    mysqli_query($conn, "DELETE FROM tb_peminjaman WHERE id=$id");
    header('Location: peminjaman.php');
    exit;
}
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
        <h2><i class="fa fa-book"></i> Buku yang Masih Dipinjam</h2>
        <table>
        <tr><th>No</th><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Deadline</th><th>Denda</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($masih)): ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= $row['tgl_pinjam'] ?></td>
        <?php
        $tgl_pinjam = new DateTime($row['tgl_pinjam']);
        $deadline = clone $tgl_pinjam;
        $deadline->modify('+7 days');
        $today = new DateTime();
        $denda = 0;
        if ($today > $deadline) {
            $selisih = $deadline->diff($today)->days;
            $denda = $selisih * 2000;
        }
        ?>
        <td><?= $deadline->format('Y-m-d') ?></td>
        <td><?= $denda ? number_format($denda,0,',','.') : '-' ?></td>
        <td>
        <a href="?edit=<?= $row['id'] ?>">Edit</a> |
        <a href="?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus peminjaman?')">Hapus</a>
        </td>
        </tr>
        <?php endwhile; ?>
        </table>
        <h2><i class="fa fa-check-circle"></i> Buku yang Sudah Dikembalikan</h2>
        <table>
        <tr><th>No</th><th>Buku</th><th>Anggota</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Deadline</th><th>Denda</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($sudah)): ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= $row['tgl_pinjam'] ?></td>
        <td><?= $row['tgl_kembali'] ?></td>
        <?php
        $tgl_pinjam = new DateTime($row['tgl_pinjam']);
        $deadline = clone $tgl_pinjam;
        $deadline->modify('+7 days');
        $tgl_kembali = new DateTime($row['tgl_kembali']);
        $denda = 0;
        if ($tgl_kembali > $deadline) {
            $selisih = $deadline->diff($tgl_kembali)->days;
            $denda = $selisih * 2000;
        }
        ?>
        <td><?= $deadline->format('Y-m-d') ?></td>
        <td><?= $denda ? number_format($denda,0,',','.') : '-' ?></td>
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