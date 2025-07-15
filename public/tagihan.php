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
        // Hitung denda
        $denda = 0;
        if ($row['tgl_kembali']) {
            $tgl_pinjam = new DateTime($row['tgl_pinjam']);
            $tgl_kembali = new DateTime($row['tgl_kembali']);
            $selisih = $tgl_pinjam->diff($tgl_kembali)->days;
            if ($selisih > 7) {
                $denda = ($selisih - 7) * 2000;
            }
        } elseif ((new DateTime())->diff(new DateTime($row['tgl_pinjam']))->days > 7) {
            // Jika belum dikembalikan dan sudah lewat 7 hari
            $selisih = (new DateTime($row['tgl_pinjam']))->diff(new DateTime())->days;
            $denda = ($selisih - 7) * 2000;
        }
        $jml_tagihan = $harga_buku + $harga_fasilitas + $denda;
        mysqli_query($conn, "INSERT INTO tb_tagihan (bulan, id_peminjaman, jml_tagihan) VALUES ('$bulan','$id_peminjaman','$jml_tagihan')");
        // Simpan denda ke tb_tagihan jika ingin, atau tampilkan saja di tabel
    }
    header('Location: tagihan.php');
    exit;
}
$tagihan = mysqli_query($conn, "SELECT t.*, a.nama, b.judul, p.tgl_pinjam, p.tgl_kembali FROM tb_tagihan t JOIN tb_peminjaman p ON t.id_peminjaman=p.id JOIN tb_anggota a ON p.id_anggota=a.id JOIN tb_buku b ON p.id_buku=b.id ORDER BY t.bulan DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Tagihan Pinjaman</title>
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
            <a href="tagihan.php" class="active"><i class="fa fa-file-invoice-dollar"></i><span>Tagihan Pinjaman</span></a>
            <a href="pembayaran.php"><i class="fa fa-money-bill"></i><span>Pembayaran</span></a>
        </nav>
        <div class="user" style="margin-top:auto;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:44px;height:44px;border-radius:50%;background:#fff2;color:#2980b9;display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:600;">
                    <?= strtoupper(substr($user['nama'],0,1)) ?>
                </div>
                <div>
                    <div style="font-weight:600;line-height:1.2;"> <?= htmlspecialchars($user['nama']) ?> </div>
                    <div style="font-size:13px;opacity:.8;line-height:1.2;"> <?= htmlspecialchars($user['username'] ?? '-') ?> </div>
                    <div style="font-size:12px;opacity:.7;">(<?= $user['role'] ?>)</div>
                </div>
            </div>
        </div>
        <a href="logout.php" class="logout"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </aside>
    <main class="main">
        <div class="dashboard-title"><i class="fa fa-file-invoice-dollar"></i> Tagihan Pinjaman</div>
        <form method="post" style="margin:10px 0;">
            <button type="submit" name="generate" onclick="return confirm('Generate tagihan bulan ini?')">Generate Tagihan Pinjaman Bulan Ini</button>
        </form>
        <table>
        <tr><th>No</th><th>Bulan</th><th>Anggota</th><th>Buku</th><th>Denda</th><th>Jumlah Tagihan</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($tagihan)):
            $denda = 0;
            if ($row['tgl_kembali']) {
                $tgl_pinjam = new DateTime($row['tgl_pinjam']);
                $tgl_kembali = new DateTime($row['tgl_kembali']);
                $selisih = $tgl_pinjam->diff($tgl_kembali)->days;
                if ($selisih > 7) {
                    $denda = ($selisih - 7) * 2000;
                }
            } elseif ((new DateTime())->diff(new DateTime($row['tgl_pinjam']))->days > 7) {
                $selisih = (new DateTime($row['tgl_pinjam']))->diff(new DateTime())->days;
                $denda = ($selisih - 7) * 2000;
            }
        ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['bulan'] ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= $denda ? number_format($denda,0,',','.') : '-' ?></td>
        <td><?= number_format($row['jml_tagihan'],0,',','.') ?></td>
        </tr>
        <?php endwhile; ?>
        </table>
    </main>
</div>
</body>
</html> 