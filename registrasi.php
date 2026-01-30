<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $nama = $_POST['nama_lengkap'] ?? '';
    $nrp = $_POST['nrp'] ?? '';
    $password = $_POST['password'] ?? '';
    $bidang = $_POST['bidang_keahlian'] ?? '';
    $hp = $_POST['no_hp'] ?? '';

    // Cek duplikasi
    $stmt = $mysqli->prepare('SELECT user_id FROM user WHERE email = ? OR nrp = ? LIMIT 1');
    $stmt->bind_param('ss', $email, $nrp);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        $error = 'Email atau NRP sudah terdaftar.';
    } else {
        $stmt = $mysqli->prepare('INSERT INTO user (email, nama_lengkap, nrp, password, bidang_keahlian, no_hp) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $email, $nama, $nrp, $password, $bidang, $hp);
        if ($stmt->execute()) {
            header('Location: login.php');
            exit;
        } else {
            $error = 'Gagal registrasi, coba lagi.';
        }
    }
}
require_once 'includes/header.php';
?>
<h2>Registrasi</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post">
  <div class="mb-3">
    <label class="form-label">Nama Lengkap</label>
    <input type="text" name="nama_lengkap" required class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" required class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">NRP</label>
    <input type="text" name="nrp" required class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Password (disimpan tanpa hash)</label>
    <input type="password" name="password" required class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Bidang Keahlian</label>
    <input type="text" name="bidang_keahlian" class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">No HP</label>
    <input type="text" name="no_hp" class="form-control">
  </div>
  <button class="btn btn-primary">Daftar</button>
</form>
<?php require_once 'includes/footer.php';?>
