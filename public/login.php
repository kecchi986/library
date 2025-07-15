<?php
session_start();
require_once '../config/database.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        // Cek apakah password di database sudah hash MD5 atau plain
        if (
            $row['password'] === $password || // plain
            $row['password'] === md5($password) // md5
        ) {
            $_SESSION['user'] = $row;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Perpustakaan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    .login-box { max-width:350px; margin:60px auto; border:1px solid #ccc; padding:30px; border-radius:8px; background:#fafbfc; }
    .login-box h2 { text-align:center; }
    .login-box input { width:100%; margin-bottom:14px; padding:8px; border-radius:4px; border:1px solid #bbb; }
    .login-box button { width:100%; background:#2980b9; color:#fff; border:none; padding:10px; border-radius:4px; font-size:16px; }
    .login-box .error { color:red; text-align:center; }
    .login-box a { display:block; text-align:center; margin-top:10px; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Login Sistem Perpustakaan</h2>
    <?php if ($error) echo '<div class="error">'.$error.'</div>'; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <a href="register.php">Belum punya akun? Daftar Anggota</a>
</div>
</body>
</html> 