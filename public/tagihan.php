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
        $tgl_pinjam = new DateTime($row['tgl_pinjam']);
        $deadline = clone $tgl_pinjam;
        $deadline->modify('+7 days');
        if ($row['tgl_kembali']) {
            $tgl_kembali = new DateTime($row['tgl_kembali']);
            if ($tgl_kembali > $deadline) {
                $selisih = $deadline->diff($tgl_kembali)->days;
                $denda = $selisih * 2000;
            }
        } else {
            $today = new DateTime();
            if ($today > $deadline) {
                $selisih = $deadline->diff($today)->days;
                $denda = $selisih * 2000;
            }
        }
        $jml_tagihan = $harga_buku + $harga_fasilitas + $denda;
        mysqli_query($conn, "INSERT INTO tb_tagihan (bulan, id_peminjaman, jml_tagihan, denda) VALUES ('$bulan','$id_peminjaman','$jml_tagihan','$denda')");
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
        <tr><th>No</th><th>Bulan</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Tagihan Pokok</th><th>Denda</th><th>Total</th><th>Status</th><th>Sisa Tagihan</th><th>Sisa Denda</th><th>Sisa Total</th><th>Aksi</th></tr>
        <?php $no=1; while($row=mysqli_fetch_assoc($tagihan)):
            // Hitung sisa tagihan, denda, total
            $id_tagihan = $row['id'];
            $tagihan_pokok = $row['jml_tagihan'] - $row['denda'];
            $denda = $row['denda'];
            $total = $row['jml_tagihan'];
            $q = mysqli_query($conn, "SELECT SUM(jml_bayar) as total_tagihan FROM tb_pembayaran WHERE id_tagihan=$id_tagihan AND (keterangan='Tagihan' OR keterangan='Tagihan+Denda')");
            $bayar_tagihan = 0;
            if ($r = mysqli_fetch_assoc($q)) $bayar_tagihan = $r['total_tagihan'] ?? 0;
            $q2 = mysqli_query($conn, "SELECT SUM(jml_bayar) as total_denda FROM tb_pembayaran WHERE id_tagihan=$id_tagihan AND (keterangan='Denda' OR keterangan='Tagihan+Denda')");
            $bayar_denda = 0;
            if ($r2 = mysqli_fetch_assoc($q2)) $bayar_denda = $r2['total_denda'] ?? 0;
            $sisa_tagihan = max(0, $tagihan_pokok - $bayar_tagihan);
            $sisa_denda = max(0, $denda - $bayar_denda);
            $sisa_total = max(0, $total - ($bayar_tagihan+$bayar_denda));
            $status = ($sisa_total==0) ? 'Lunas' : 'Belum Lunas';
            $badge = $status=='Lunas' ? '<span style="background:#27ae60;color:#fff;padding:2px 10px;border-radius:12px;font-size:13px;">Lunas</span>' : '<span style="background:#f39c12;color:#fff;padding:2px 10px;border-radius:12px;font-size:13px;">Belum Lunas</span>';
        ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['bulan'] ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= htmlspecialchars($row['judul']) ?></td>
        <td><?= $row['tgl_pinjam'] ?></td>
        <td><?= $row['tgl_kembali'] ?? '-' ?></td>
        <td><?= number_format($tagihan_pokok,0,',','.') ?></td>
        <td><?= number_format($denda,0,',','.') ?></td>
        <td><?= number_format($total,0,',','.') ?></td>
        <td><?= $badge ?></td>
        <td><?= number_format($sisa_tagihan,0,',','.') ?></td>
        <td><?= number_format($sisa_denda,0,',','.') ?></td>
        <td><?= number_format($sisa_total,0,',','.') ?></td>
        <td><a href="pembayaran.php?id_tagihan=<?= $id_tagihan ?>" style="color:#2980b9;font-weight:600;">Bayar</a></td>
        </tr>
        <?php endwhile; ?>
        </table>
    </main>
</div>
</body>
</html> 