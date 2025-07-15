<?php
require_once '../config/database.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    if ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username sudah terdaftar!';
        } else {
            $passhash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "INSERT INTO users (username, password, nama, role) VALUES ('$username', '$passhash', '$nama', 'anggota')");
            $id_user = mysqli_insert_id($conn);
            mysqli_query($conn, "INSERT INTO anggota (nama, alamat, telepon) VALUES ('$nama', '$alamat', '$telepon')");
            $success = 'Registrasi berhasil! Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Anggota</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: linear-gradient(135deg, #2980b9 0%, #6dd5fa 100%); min-height:100vh;">
<div style="display:flex;justify-content:center;align-items:center;min-height:100vh;">
    <div style="background:#fff;max-width:400px;width:100%;border-radius:16px;box-shadow:0 2px 16px #0002;padding:36px 28px;">
        <div style="text-align:center;margin-bottom:24px;">
            <div style="font-size:2.2rem;color:#2980b9;font-weight:700;letter-spacing:1px;"><i class="fa fa-book"></i> Perpustakaan</div>
            <div style="font-size:1.1rem;color:#6c7a89;margin-top:6px;">Registrasi Anggota</div>
        </div>
        <?php if ($error) echo '<div class="error">'.$error.'</div>'; ?>
        <?php if ($success) echo '<div class="success">'.$success.'</div>'; ?>
        <form method="post">
            <input type="text" name="nama" placeholder="Nama Lengkap" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="konfirmasi" placeholder="Konfirmasi Password" required>
            <textarea name="alamat" placeholder="Alamat" required></textarea>
            <input type="text" name="telepon" placeholder="Telepon" required>
            <button type="submit"><i class="fa fa-user-plus"></i> Daftar</button>
        </form>
        <a href="login.php" style="display:block;text-align:center;margin-top:18px;">Sudah punya akun? Login</a>
    </div>
</div>
</body>
</html> 