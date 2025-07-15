<?php
require_once '../config/database.php';
$users = [
    ['admin', 'admin123', 'Administrator', 'admin'],
    ['petugas', 'petugas123', 'Petugas Satu', 'petugas'],
    ['anggota', 'anggota123', 'Anggota Satu', 'anggota'],
];
$ok = 0;
foreach ($users as $u) {
    $username = $u[0];
    $password = password_hash($u[1], PASSWORD_DEFAULT);
    $nama = $u[2];
    $role = $u[3];
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) == 0) {
        if (mysqli_query($conn, "INSERT INTO users (username, password, nama, role) VALUES ('$username', '$password', '$nama', '$role')")) {
            $ok++;
        }
    }
}
echo "<div style='font-family:Segoe UI,Arial,sans-serif;margin:40px auto;max-width:400px;padding:32px 24px;background:#fff;border-radius:10px;box-shadow:0 2px 12px #0001;text-align:center;'>";
echo "<h2>Seed User Sukses</h2>";
echo "<div>Ditambahkan: $ok user baru.<br><br><b>admin/admin123</b><br><b>petugas/petugas123</b><br><b>anggota/anggota123</b></div>";
echo "<div style='margin-top:24px;color:#888;font-size:14px;'>Setelah selesai, hapus file ini demi keamanan.</div>";
echo "</div>"; 