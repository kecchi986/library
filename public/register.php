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
</head>
<body>
<div class="container">
    <h1 style="text-align:center; color:#2980b9; margin-bottom:32px;">Registrasi Anggota</h1>
    <div class="register-box">
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
</div>
</body>
</html> 