<?php
require_once '../config/database.php';
$username = 'admin';
$newpass = 'admin123';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hash = password_hash($newpass, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password='$hash' WHERE username='$username' AND role='admin'";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='success'>Password admin berhasil direset ke: <b>$newpass</b></div>";
    } else {
        $msg = "<div class='error'>Gagal reset password: ".mysqli_error($conn)."</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container" style="max-width:400px; margin-top:60px;">
    <h2 style="text-align:center; color:#c0392b;">Reset Password Admin</h2>
    <?= $msg ?>
    <form method="post" style="text-align:center;">
        <button type="submit" onclick="return confirm('Reset password admin ke admin123?')">Jalankan Reset</button>
    </form>
    <div style="margin-top:24px; color:#888; font-size:14px; text-align:center;">Setelah selesai, hapus file ini demi keamanan.</div>
</div>
</body>
</html> 