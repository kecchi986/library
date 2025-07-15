<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','petugas'])) {
    header('Location: login.php');
    exit;
}
// Generate tagihan bulan ini
if (isset($_POST['generate'])) {
    $bulan = date('Y-m');
    // Ambil semua peminjaman bulan ini yang belum ada tagihan
    $peminjaman = mysqli_query($conn, "SELECT p.*, a.id as id_anggota FROM tb_peminjaman p JOIN tb_anggota a ON p.id_anggota=a.id WHERE DATE_FORMAT(p.tgl_pinjam,'%Y-%m')='$bulan' AND p.id NOT IN (SELECT id_peminjaman FROM tb_tagihan WHERE bulan='$bulan')");
    while($row=mysqli_fetch_assoc($peminjaman)) {
        $id_peminjaman = $row['id'];
        $id_anggota = $row['id_anggota'];
        // Harga buku
        $buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT harga FROM tb_buku WHERE id=".$row['id_buku']));
        $harga_buku = $buku ? $buku['harga'] : 0;
        // Total harga fasilitas anggota
        $q = mysqli_query($conn, "SELECT SUM(f.harga) as total FROM tb_fasilitas_anggota fa JOIN tb_fasilitas f ON fa.id_fasilitas=f.id WHERE fa.id_anggota=$id_anggota");
        $fasilitas = mysqli_fetch_assoc($q);
        $harga_fasilitas = $fasilitas['total'] ?? 0;
        $jml_tagihan = $harga_buku + $harga_fasilitas;
        mysqli_query($conn, "INSERT INTO tb_tagihan (bulan, id_peminjaman, jml_tagihan) VALUES ('$bulan','$id_peminjaman','$jml_tagihan')");
    }
    header('Location: tagihan.php');
    exit;
}
// Tampilkan tagihan
$tagihan = mysqli_query($conn, "SELECT t.*, a.nama, b.judul FROM tb_tagihan t JOIN tb_peminjaman p ON t.id_peminjaman=p.id JOIN tb_anggota a ON p.id_anggota=a.id JOIN tb_buku b ON p.id_buku=b.id ORDER BY t.bulan DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Tagihan Bulanan</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
<h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Data Tagihan Bulanan</h1>
<a href="dashboard.php">Kembali ke Dashboard</a>
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
</div>
</body>
</html> 