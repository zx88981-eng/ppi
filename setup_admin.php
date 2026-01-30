<?php
require_once 'config.php';
// Jika ada admin, jangan lanjut
$r = $mysqli->query('SELECT count(*) as c FROM admin')->fetch_assoc();
if ($r['c'] > 0) {
    die('Sudah ada admin. Hapus file ini setelah setup.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? 'Admin';
    $email = $_POST['email'] ?? 'admin@example.com';
    $password = $_POST['password'] ?? 'admin';
    $stmt = $mysqli->prepare('INSERT INTO admin (nama, email, password, role) VALUES (?, ?, ?, "head_admin")');
    $stmt->bind_param('sss', $nama, $email, $password);
    if ($stmt->execute()) {
        echo 'Admin dibuat. Silakan hapus file setup_admin.php setelah selesai.';
        exit;
    } else {
        echo 'Gagal membuat admin.';
    }
}
?>
<form method="post">
  <div><label>Nama: <input name="nama"></label></div>
  <div><label>Email: <input name="email"></label></div>
  <div><label>Password (plain): <input name="password"></label></div>
  <button>Buatan Admin</button>
</form>