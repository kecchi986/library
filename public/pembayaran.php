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
$alert = '';
// Tambah: Ambil denda dari tb_tagihan
function getDenda($conn, $id_tagihan) {
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT denda FROM tb_tagihan WHERE id=$id_tagihan"));
    return $row ? $row['denda'] : 0;
}
// Cek jika tidak ada tagihan yang tersedia
$q_tagihan_cek = mysqli_query($conn, "SELECT COUNT(*) as jml FROM tb_tagihan");
$cek_tagihan = mysqli_fetch_assoc($q_tagihan_cek);
if ($cek_tagihan['jml'] == 0) {
    $alert = "<div class='alert' style='background:#ffe0e0;color:#b00;padding:12px 18px;margin:12px 0;border-radius:6px;'><b>Belum ada tagihan yang digenerate! Silakan generate tagihan di menu Tagihan sebelum melakukan pembayaran.</b></div>";
}
// Proses tambah pembayaran
if (isset($_POST['tambah'])) {
    $id_tagihan = $_POST['id_tagihan'];
    $jml_bayar = $_POST['jml_bayar'];
    $jenis = $_POST['jenis_bayar']; // 'tagihan', 'denda', 'semua'
    $row_tagihan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT jml_tagihan, denda FROM tb_tagihan WHERE id=$id_tagihan"));
    $total_bayar = 0;
    $q = mysqli_query($conn, "SELECT SUM(jml_bayar) as total FROM tb_pembayaran WHERE id_tagihan=$id_tagihan");
    if ($r = mysqli_fetch_assoc($q)) $total_bayar = $r['total'] ?? 0;
    $sisa_tagihan = $row_tagihan['jml_tagihan'] - $row_tagihan['denda'];
    $sisa_denda = $row_tagihan['denda'];
    $bayar_tagihan = 0; $bayar_denda = 0;
    if ($jenis=='tagihan') {
        $bayar_tagihan = min($jml_bayar, $sisa_tagihan);
    } elseif ($jenis=='denda') {
        $bayar_denda = min($jml_bayar, $sisa_denda);
    } else {
        // semua
        if ($jml_bayar <= $sisa_tagihan) {
            $bayar_tagihan = $jml_bayar;
        } else {
            $bayar_tagihan = $sisa_tagihan;
            $bayar_denda = $jml_bayar - $sisa_tagihan;
            if ($bayar_denda > $sisa_denda) $bayar_denda = $sisa_denda;
        }
    }
    $total_bayar_baru = $total_bayar + $jml_bayar;
    $status = ($total_bayar_baru >= $row_tagihan['jml_tagihan']) ? 'lunas' : 'cicil';
    if ($jml_bayar > ($sisa_tagihan+$sisa_denda)) {
        $alert = "<div class='alert' style='background:#ffe0e0;color:#b00;padding:12px 18px;margin:12px 0;border-radius:6px;'><b>Jumlah bayar melebihi sisa tagihan+denda!</b></div>";
    } else {
        $keterangan = $jenis=='tagihan' ? 'Tagihan' : ($jenis=='denda' ? 'Denda' : 'Tagihan+Denda');
        mysqli_query($conn, "INSERT INTO tb_pembayaran (id_tagihan, jml_bayar, status, keterangan) VALUES ($id_tagihan, $jml_bayar, '$status', '$keterangan')");
        // Update status semua pembayaran tagihan ini
        mysqli_query($conn, "UPDATE tb_pembayaran SET status='$status' WHERE id_tagihan=$id_tagihan");
        header('Location: pembayaran.php');
        exit;
    }
}
// Proses edit pembayaran
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $id_tagihan = $_POST['id_tagihan'];
    $jml_bayar = $_POST['jml_bayar'];
    $jenis = $_POST['jenis_bayar'];
    $row_tagihan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT jml_tagihan, denda FROM tb_tagihan WHERE id=$id_tagihan"));
    $q = mysqli_query($conn, "SELECT SUM(jml_bayar) as total FROM tb_pembayaran WHERE id_tagihan=$id_tagihan AND id<>$id");
    $total_bayar = 0;
    if ($r = mysqli_fetch_assoc($q)) $total_bayar = $r['total'] ?? 0;
    $sisa_tagihan = $row_tagihan['jml_tagihan'] - $row_tagihan['denda'];
    $sisa_denda = $row_tagihan['denda'];
    $bayar_tagihan = 0; $bayar_denda = 0;
    if ($jenis=='tagihan') {
        $bayar_tagihan = min($jml_bayar, $sisa_tagihan);
    } elseif ($jenis=='denda') {
        $bayar_denda = min($jml_bayar, $sisa_denda);
    } else {
        if ($jml_bayar <= $sisa_tagihan) {
            $bayar_tagihan = $jml_bayar;
        } else {
            $bayar_tagihan = $sisa_tagihan;
            $bayar_denda = $jml_bayar - $sisa_tagihan;
            if ($bayar_denda > $sisa_denda) $bayar_denda = $sisa_denda;
        }
    }
    $total_bayar_baru = $total_bayar + $jml_bayar;
    $status = ($total_bayar_baru >= $row_tagihan['jml_tagihan']) ? 'lunas' : 'cicil';
    if ($jml_bayar > ($sisa_tagihan+$sisa_denda)) {
        $alert = "<div class='alert' style='background:#ffe0e0;color:#b00;padding:12px 18px;margin:12px 0;border-radius:6px;'><b>Jumlah bayar melebihi sisa tagihan+denda!</b></div>";
    } else {
        $keterangan = $jenis=='tagihan' ? 'Tagihan' : ($jenis=='denda' ? 'Denda' : 'Tagihan+Denda');
        mysqli_query($conn, "UPDATE tb_pembayaran SET id_tagihan=$id_tagihan, jml_bayar=$jml_bayar, status='$status', keterangan='$keterangan' WHERE id=$id");
        // Update status semua pembayaran tagihan ini
        mysqli_query($conn, "UPDATE tb_pembayaran SET status='$status' WHERE id_tagihan=$id_tagihan");
        header('Location: pembayaran.php');
        exit;
    }
}
// Proses hapus pembayaran
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_tagihan FROM tb_pembayaran WHERE id=$id"));
    $id_tagihan = $row ? $row['id_tagihan'] : 0;
    mysqli_query($conn, "DELETE FROM tb_pembayaran WHERE id=$id");
    if ($id_tagihan) {
        $row_tagihan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT jml_tagihan FROM tb_tagihan WHERE id=$id_tagihan"));
        $q = mysqli_query($conn, "SELECT SUM(jml_bayar) as total FROM tb_pembayaran WHERE id_tagihan=$id_tagihan");
        $total_bayar = 0;
        if ($r = mysqli_fetch_assoc($q)) $total_bayar = $r['total'] ?? 0;
        $status = ($total_bayar >= $row_tagihan['jml_tagihan']) ? 'lunas' : 'cicil';
        mysqli_query($conn, "UPDATE tb_pembayaran SET status='$status' WHERE id_tagihan=$id_tagihan");
    }
    header('Location: pembayaran.php');
    exit;
}
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
        <?php if($alert) echo $alert; ?>
        <div class="dashboard-title"><i class="fa fa-money-bill"></i> Data Pembayaran</div>
        <table>
        <tr><th>No</th><th>Anggota</th><th>Bulan</th><th>Tagihan</th><th>Denda</th><th>Jumlah Bayar</th><th>Jenis</th><th>Sisa</th><th>Status</th><th>Aksi</th></tr>
        <?php $no=1; $tagihan_map=[]; $qtagihan=mysqli_query($conn,"SELECT t.id, t.jml_tagihan, t.denda FROM tb_tagihan t"); while($r=mysqli_fetch_assoc($qtagihan)) $tagihan_map[$r['id']]=['jml_tagihan'=>$r['jml_tagihan'],'denda'=>$r['denda']]; mysqli_data_seek($pembayaran,0); while($row=mysqli_fetch_assoc($pembayaran)):
        $tagihan_val=$tagihan_map[$row['id_tagihan']]['jml_tagihan']??0;
        $denda_val=$tagihan_map[$row['id_tagihan']]['denda']??0;
        $total_bayar=0;
        $qsum=mysqli_query($conn,"SELECT SUM(jml_bayar) as total FROM tb_pembayaran WHERE id_tagihan=".$row['id_tagihan']);
        if($rsum=mysqli_fetch_assoc($qsum)) $total_bayar=$rsum['total']??0;
        $sisa=$tagihan_val-$total_bayar;
        ?>
        <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td><?= $row['bulan'] ?></td>
        <td><?= number_format($tagihan_val-$denda_val,0,',','.') ?></td>
        <td><?= number_format($denda_val,0,',','.') ?></td>
        <td><?= number_format($row['jml_bayar'],0,',','.') ?></td>
        <td><?= $row['keterangan'] ?? '-' ?></td>
        <td><?= number_format($sisa,0,',','.') ?></td>
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
        Tagihan: <select name="id_tagihan" required onchange="this.form.submit()">
        <option value="">Pilih Tagihan</option>
        <?php while($t=mysqli_fetch_assoc($tagihan)):
            $selected = isset($edit)&&$edit['id_tagihan']==$t['id'] ? 'selected' : (isset($_POST['id_tagihan'])&&$_POST['id_tagihan']==$t['id']?'selected':''); ?>
        <option value="<?= $t['id'] ?>" <?= $selected ?>><?= htmlspecialchars($t['nama']).' - '.$t['bulan'] ?></option>
        <?php endwhile; ?>
        </select><br>
        <?php
        $tagihan_id = $edit['id_tagihan'] ?? ($_POST['id_tagihan'] ?? '');
        $nominal_tagihan = 0; $denda_tagihan = 0;
        if ($tagihan_id) {
            $row_tagihan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT jml_tagihan, denda FROM tb_tagihan WHERE id=$tagihan_id"));
            $nominal_tagihan = $row_tagihan ? $row_tagihan['jml_tagihan'] : 0;
            $denda_tagihan = $row_tagihan ? $row_tagihan['denda'] : 0;
            $q = mysqli_query($conn, "SELECT SUM(jml_bayar) as total FROM tb_pembayaran WHERE id_tagihan=$tagihan_id" . ((isset($edit['id']) && $edit) ? " AND id<>".$edit['id'] : ""));
            $total_bayar = 0;
            if ($r = mysqli_fetch_assoc($q)) $total_bayar = $r['total'] ?? 0;
            $sisa = $nominal_tagihan - $total_bayar;
            $sisa_tagihan = $nominal_tagihan - $denda_tagihan;
            echo '<div style="margin:8px 0 8px 0;font-size:15px;color:#2980b9;">Tagihan Pokok: <b>'.number_format($sisa_tagihan,0,',','.').'</b> | Denda: <b>'.number_format($denda_tagihan,0,',','.').'</b> | Sisa Total: <b>'.number_format($sisa,0,',','.').'</b></div>';
        }
        ?>
        Jenis Pembayaran: <select name="jenis_bayar" required>
            <option value="tagihan">Tagihan Saja</option>
            <option value="denda">Denda Saja</option>
            <option value="semua">Tagihan + Denda</option>
        </select><br>
        Jumlah Bayar: <input type="number" name="jml_bayar" value="<?= $edit['jml_bayar'] ?? ($_POST['jml_bayar'] ?? 0) ?>" required><br>
        <button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Tambah' ?></button>
        </form>
    </main>
</div>
</body>
</html> 