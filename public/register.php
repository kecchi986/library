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
            $passmd5 = md5($password);
            mysqli_query($conn, "INSERT INTO users (username, password, nama, role) VALUES ('$username', '$passmd5', '$nama', 'anggota')");
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
    <style>
    .register-box { max-width:400px; margin:40px auto; border:1px solid #ccc; padding:30px; border-radius:8px; background:#fafbfc; }
    .register-box h2 { text-align:center; }
    .register-box input, .register-box textarea { width:100%; margin-bottom:12px; padding:8px; border-radius:4px; border:1px solid #bbb; }
    .register-box button { width:100%; background:#2980b9; color:#fff; border:none; padding:10px; border-radius:4px; font-size:16px; }
    .register-box .error { color:red; text-align:center; }
    .register-box .success { color:green; text-align:center; }
    .register-box a { display:block; text-align:center; margin-top:10px; }
    </style>
</head>
<body>
<div class="register-box">
    <h2>Registrasi Anggota</h2>
    <?php if ($error) echo '<div class="error">'.$error.'</div>'; ?>
    <?php if ($success) echo '<div class="success">'.$success.'</div>'; ?>
    <form method="post">
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="konfirmasi" placeholder="Konfirmasi Password" required>
        <textarea name="alamat" placeholder="Alamat" required></textarea>
        <input type="text" name="telepon" placeholder="Telepon" required>
        <button type="submit">Daftar</button>
    </form>
    <a href="login.php">Sudah punya akun? Login</a>
</div>
</body>
</html> 