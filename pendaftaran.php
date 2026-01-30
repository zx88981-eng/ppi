<?php
require_once 'config.php';
if (!is_user_logged_in()) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil list dosen
$dosen_res = $mysqli->query('SELECT * FROM dosen ORDER BY nama_dosen');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_pendaftar'] ?? '';
    $judul = $_POST['judul'] ?? '';
    $nama_dosen = $_POST['nama_dosen'] ?? '';
    $pembimbing_lapangan = $_POST['pembimbing_lapangan'] ?? '';
    $no_telp = trim($_POST['no_telp'] ?? '');

    // Batas ukuran file (dalam bytes)
    $MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

    // Helper: parse php.ini size seperti '2M' menjadi byte
    function parse_ini_size($size) {
        $unit = strtolower(substr($size, -1));
        $num = (int)$size;
        switch ($unit) {
            case 'g': return $num * 1024 * 1024 * 1024;
            case 'm': return $num * 1024 * 1024;
            case 'k': return $num * 1024;
            default: return (int)$size;
        }
    }

    $php_upload_max = parse_ini_size(ini_get('upload_max_filesize'));
    if ($php_upload_max > 0 && $php_upload_max < $MAX_FILE_SIZE) {
        $warning_upload_limit = 'Perhatian: pengaturan server `upload_max_filesize` = ' . ini_get('upload_max_filesize') . ' (lebih kecil dari batas aplikasi 5M). Unggah file besar mungkin gagal.';
    } else {
        $warning_upload_limit = '';
    }

    // Validasi: semua field harus diisi
    $missing = [];
    if (trim($nama) === '') $missing[] = 'Nama';
    if (trim($judul) === '') $missing[] = 'Judul';
    if (trim($nama_dosen) === '') $missing[] = 'Dosen Pembimbing';
    if (trim($pembimbing_lapangan) === '') $missing[] = 'Pembimbing Lapangan';
    if ($no_telp === '') $missing[] = 'No Telp';

    if (!empty($missing)) {
        $error = 'Harap isi semua field: ' . implode(', ', $missing) . '.';
    }

    // Validasi no_telp hanya angka
    if (empty($error) && !preg_match('/^\d+$/', $no_telp)) {
        $error = 'No Telp harus berupa angka saja (tanpa spasi atau simbol).';
    }

    // Validasi file upload: semua file harus diunggah dan tidak melebihi batas ukuran
    $missing_files = [];
    if (empty($error)) {
        if (empty($_FILES['file_kesediaan_pembimbing']) || $_FILES['file_kesediaan_pembimbing']['error'] !== UPLOAD_ERR_OK) $missing_files[] = 'File Kesediaan Pembimbing';
        if (empty($_FILES['file_kesediaan_praktik']) || $_FILES['file_kesediaan_praktik']['error'] !== UPLOAD_ERR_OK) $missing_files[] = 'File Kesediaan Praktik';
        if (empty($_FILES['file_proposal']) || $_FILES['file_proposal']['error'] !== UPLOAD_ERR_OK) $missing_files[] = 'File Proposal';

        if (!empty($missing_files)) {
            $error = 'Harap unggah semua file: ' . implode(', ', $missing_files) . '.';
        }

        // Cek ukuran masing-masing file
        if (empty($error)) {
            if (isset($_FILES['file_kesediaan_pembimbing']) && $_FILES['file_kesediaan_pembimbing']['error'] === UPLOAD_ERR_OK && $_FILES['file_kesediaan_pembimbing']['size'] > $MAX_FILE_SIZE) {
                $error = 'File Kesediaan Pembimbing terlalu besar. Maks 5MB.';
            }
            if (isset($_FILES['file_kesediaan_praktik']) && $_FILES['file_kesediaan_praktik']['error'] === UPLOAD_ERR_OK && $_FILES['file_kesediaan_praktik']['size'] > $MAX_FILE_SIZE) {
                $error = 'File Kesediaan Praktik terlalu besar. Maks 5MB.';
            }
            if (isset($_FILES['file_proposal']) && $_FILES['file_proposal']['error'] === UPLOAD_ERR_OK && $_FILES['file_proposal']['size'] > $MAX_FILE_SIZE) {
                $error = 'File Proposal terlalu besar. Maks 5MB.';
            }
        }
    }

    // hanya lakukan upload dan penyimpanan jika tidak ada error
    if (empty($error)) {
        // upload file kesediaan pembimbing
        $file_kesediaan_pembimbing = null;
        if (!empty($_FILES['file_kesediaan_pembimbing']) && $_FILES['file_kesediaan_pembimbing']['error'] === UPLOAD_ERR_OK) {
            $fn = basename($_FILES['file_kesediaan_pembimbing']['name']);
            $dest = __DIR__ . '/uploads/' . time() . '_kb_' . $fn;
            if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
            move_uploaded_file($_FILES['file_kesediaan_pembimbing']['tmp_name'], $dest);
            $file_kesediaan_pembimbing = 'uploads/' . basename($dest);
        }

        // upload file kesediaan praktik
        $file_kesediaan_praktik = null;
        if (!empty($_FILES['file_kesediaan_praktik']) && $_FILES['file_kesediaan_praktik']['error'] === UPLOAD_ERR_OK) {
            $fn = basename($_FILES['file_kesediaan_praktik']['name']);
            $dest = __DIR__ . '/uploads/' . time() . '_kp_' . $fn;
            if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
            move_uploaded_file($_FILES['file_kesediaan_praktik']['tmp_name'], $dest);
            $file_kesediaan_praktik = 'uploads/' . basename($dest);
        }

        // upload file proposal
        $file_proposal_path = null;
        if (!empty($_FILES['file_proposal']) && $_FILES['file_proposal']['error'] === UPLOAD_ERR_OK) {
            $fn = basename($_FILES['file_proposal']['name']);
            $dest = __DIR__ . '/uploads/' . time() . '_' . $fn;
            if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
            move_uploaded_file($_FILES['file_proposal']['tmp_name'], $dest);
            $file_proposal_path = 'uploads/' . basename($dest);
        }

        $stmt = $mysqli->prepare('INSERT INTO pendaftaran (user_id, nama_pendaftar, judul, nama_dosen, pembimbing_lapangan, no_telp, file_kesediaan_pembimbing, file_kesediaan_praktik, file_proposal, status_pendaftaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "diajukan")');
        $stmt->bind_param('issssssss', $user_id, $nama, $judul, $nama_dosen, $pembimbing_lapangan, $no_telp, $file_kesediaan_pembimbing, $file_kesediaan_praktik, $file_proposal_path);
        if ($stmt->execute()) {
            header('Location: tunggu.php');
            exit;
        } else {
            $error = 'Gagal mengajukan pendaftaran.';
        }
    }
}

require_once 'includes/header.php';
?>
<h2>Pendaftaran PPI</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label class="form-label">Nama <span class="text-danger">*</span></label>
    <input type="text" name="nama_pendaftar" required class="form-control" value="<?=htmlspecialchars($_SESSION['user_name'] ?? '')?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Judul <span class="text-danger">*</span></label>
    <input type="text" name="judul" required class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Dosen Pembimbing (pilih) <span class="text-danger">*</span></label>
    <select name="nama_dosen" required class="form-select">
      <?php while ($d = $dosen_res->fetch_assoc()): ?>
        <option value="<?=htmlspecialchars($d['nama_dosen'])?>"><?=htmlspecialchars($d['nama_dosen'])?> - <?=htmlspecialchars($d['bidang_keahlian'])?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Pembimbing Lapangan <span class="text-danger">*</span></label>
    <input type="text" name="pembimbing_lapangan" required class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">No Telp <span class="text-danger">*</span></label>
    <input type="tel" name="no_telp" required class="form-control" pattern="\d+" inputmode="numeric" maxlength="15" title="Masukkan angka saja (tanpa spasi atau simbol)">
  </div>
  <div class="mb-3">
    <label class="form-label">File Kesediaan Pembimbing <span class="text-danger">*</span></label>
    <input type="file" name="file_kesediaan_pembimbing" required class="form-control">
    <small class="form-text text-muted">Maks 5MB per file.</small>
  </div>
  <div class="mb-3">
    <label class="form-label">File Kesediaan Praktik <span class="text-danger">*</span></label>
    <input type="file" name="file_kesediaan_praktik" required class="form-control">
    <small class="form-text text-muted">Maks 5MB per file.</small>
  </div>
  <div class="mb-3">
    <label class="form-label">File Proposal <span class="text-danger">*</span></label>
    <input type="file" name="file_proposal" required class="form-control">
    <small class="form-text text-muted">Maks 5MB per file.</small>
  </div>
  <?php if (!empty($warning_upload_limit)): ?>
    <div class="alert alert-warning"><?=htmlspecialchars($warning_upload_limit)?></div>
  <?php endif; ?>
  <button class="btn btn-primary">Ajukan</button>
</form>
<?php require_once 'includes/footer.php';?>