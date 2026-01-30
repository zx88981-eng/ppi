<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? ''); // NRP for users or email for admin
    $password = $_POST['password'] ?? '';

    // Cek admin terlebih dahulu (cari berdasarkan kolom email tanpa memaksa format email)
    $stmt = $mysqli->prepare('SELECT admin_id, nama, password FROM admin WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $identifier);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        // Password disimpan tanpa hash sesuai permintaan
        if ($row['password'] === $password) {
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_name'] = $row['nama'];
            header('Location: admin/dashboard.php');
            exit;
        }
    }

    // Cek user menggunakan NRP
    $stmt = $mysqli->prepare('SELECT user_id, nama_lengkap, password, status FROM user WHERE nrp = ? LIMIT 1');
    $stmt->bind_param('s', $identifier);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        if ($row['password'] === $password) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user_name'] = $row['nama_lengkap'];
            $_SESSION['user_status'] = $row['status'];
            
            // Redirect berdasarkan status verifikasi
            if ($row['status'] === 'terverifikasi') {
                header('Location: final.php');
            } else {
                // Cek apakah user sudah melakukan pendaftaran
                $check_stmt = $mysqli->prepare('SELECT pendaftar_id FROM pendaftaran WHERE user_id = ? LIMIT 1');
                $check_stmt->bind_param('i', $row['user_id']);
                $check_stmt->execute();
                $check_res = $check_stmt->get_result();
                $has_pendaftaran = $check_res && $check_res->fetch_assoc();
                
                if ($has_pendaftaran) {
                    header('Location: tunggu.php');
                } else {
                    header('Location: pendaftaran.php');
                }
            }
            exit;
        }
    }

    $error = 'NRP/Email atau password salah.';
}

require_once 'includes/header.php';
?>
<h2>Login</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post">
  <div class="mb-3">
    <label class="form-label">NRP (user) atau Email (admin)</label>
    <input type="text" name="identifier" required class="form-control" placeholder="Masukkan NRP atau Email">
  </div>
  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" name="password" required class="form-control">
  </div>
  <button class="btn btn-primary">Login</button>
</form>
<?php require_once 'includes/footer.php';?>
