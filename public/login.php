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
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row;
            header('Location: dashboard.php');
            exit;
        } elseif ($row['password'] === md5($password)) {
            $newhash = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$newhash' WHERE id=".$row['id']);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="background: linear-gradient(135deg, #2980b9 0%, #6dd5fa 100%); min-height:100vh;">
<div style="display:flex;justify-content:center;align-items:center;min-height:100vh;">
    <div style="background:#fff;max-width:370px;width:100%;border-radius:16px;box-shadow:0 2px 16px #0002;padding:36px 28px;">
        <div style="text-align:center;margin-bottom:24px;">
            <div style="font-size:2.2rem;color:#2980b9;font-weight:700;letter-spacing:1px;"><i class="fa fa-book"></i> Perpustakaan</div>
            <div style="font-size:1.1rem;color:#6c7a89;margin-top:6px;">Login Sistem</div>
        </div>
        <?php if ($error) echo '<div class="error">'.$error.'</div>'; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit"><i class="fa fa-sign-in-alt"></i> Login</button>
        </form>
        <a href="register.php" style="display:block;text-align:center;margin-top:18px;">Belum punya akun? Daftar Anggota</a>
    </div>
</div>
</body>
</html> 