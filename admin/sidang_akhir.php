<?php
require_once __DIR__ . '/../config.php';
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}

$info = '';
$error = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sidang_akhir_id = intval($_POST['sidang_akhir_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    // Save a single field (tanggal_sidang)
    if ($action === 'save_field' && $sidang_akhir_id > 0 && isset($_POST['field']) && isset($_POST['value'])) {
        $allowed = ['tanggal_sidang'];
        $field = $_POST['field'];
        $value = trim($_POST['value']);
        if (!in_array($field, $allowed)) {
            $error = 'Field tidak valid.';
        } else {
            // Check if sidang_akhir exists
            $stmt = $mysqli->prepare('SELECT sidang_akhir_id FROM sidang_akhir WHERE sidang_akhir_id = ? LIMIT 1');
            $stmt->bind_param('i', $sidang_akhir_id);
            $stmt->execute();
            $r = $stmt->get_result()->fetch_assoc();
            if ($r) {
                $sql = "UPDATE sidang_akhir SET {$field} = ? WHERE sidang_akhir_id = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param('si', $value, $sidang_akhir_id);
                $stmt->execute();
            } else {
                $error = 'Data sidang akhir tidak ditemukan.';
            }
            $info = 'Disimpan.';
            // Jika request AJAX, kembalikan JSON tanpa redirect
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'ok', 'message' => 'Disimpan']);
                exit;
            }
        }
        header('Location: sidang_akhir.php'); exit;
    }

    // Kirim email notifikasi untuk baris ini
    if ($action === 'kirim_email' && $sidang_akhir_id > 0) {
        // pastikan data sidang akhir ada
        $stmt = $mysqli->prepare('SELECT s.sidang_akhir_id, u.email, u.nama_lengkap, s.tanggal_sidang FROM sidang_akhir s JOIN user u ON u.user_id = s.user_id WHERE s.sidang_akhir_id = ? LIMIT 1');
        $stmt->bind_param('i', $sidang_akhir_id);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        if (!$r) { 
            $error = 'Data sidang akhir tidak ditemukan.'; 
        } else {
            $display_date = !empty($r['tanggal_sidang']) ? (new DateTime($r['tanggal_sidang']))->format('d-m-Y') : '-';
            $to = $r['email'];
            $subject = 'Informasi Jadwal Sidang Akhir PPI';
            $message = "Halo {$r['nama_lengkap']},\n\nSidang akhir Anda telah dijadwalkan.\nTanggal: {$display_date}\n\nSilakan siapkan laporan akhir Anda.\n\nSalam.";
            $sent = send_email_smtp($to, $subject, $message);
            if ($sent) {
                $info = 'Email berhasil dikirim.';
            } else {
                $error = 'Gagal mengirim email (cek log).';
            }
        }
        header('Location: sidang_akhir.php'); exit;
    }

}

// Ambil data sidang_akhir dengan user info
$res = $mysqli->query('SELECT s.*, u.nama_lengkap, p.nama_pendaftar FROM sidang_akhir s JOIN user u ON u.user_id = s.user_id LEFT JOIN pendaftaran p ON p.user_id = s.user_id ORDER BY s.created_at DESC');

require_once __DIR__ . '/../includes/header.php';
?>
<h2>Sidang Akhir</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if (!empty($info)): ?><div class="alert alert-success"><?=htmlspecialchars($info)?></div><?php endif; ?>
<table class="table table-bordered table-sm">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nama</th>
      <th>File Laporan Akhir</th>
      <th>File LOA Publikasi</th>
      <th>Tanggal Pengajuan</th>
      <th>Tanggal Sidang</th>
      <th>Kirim Email</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($row = $res->fetch_assoc()): ?>
    <tr>
      <td><?= $row['sidang_akhir_id'] ?></td>
      <td><?= htmlspecialchars($row['nama_pendaftar'] ?? $row['nama_lengkap'] ?? 'N/A') ?></td>
      <td>
        <?php if ($row['file_laporan_akhir']): ?>
          <a href="<?= htmlspecialchars($row['file_laporan_akhir']) ?>" target="_blank"><?= htmlspecialchars(basename($row['file_laporan_akhir'])) ?></a>
        <?php else: ?>
          <span class="text-muted">-</span>
        <?php endif; ?>
      </td>
      <td>
        <?php if ($row['file_loa_publikasi']): ?>
          <a href="<?= htmlspecialchars($row['file_loa_publikasi']) ?>" target="_blank"><?= htmlspecialchars(basename($row['file_loa_publikasi'])) ?></a>
        <?php else: ?>
          <span class="text-muted">-</span>
        <?php endif; ?>
      </td>
      <td>
        <?= !empty($row['tanggal_pengajuan']) ? (new DateTime($row['tanggal_pengajuan']))->format('d-m-Y') : '-' ?>
      </td>
      <td>
        <input type="date" class="form-control form-control-sm autosave" data-field="tanggal_sidang" data-id="<?= $row['sidang_akhir_id'] ?>" value="<?=htmlspecialchars($row['tanggal_sidang'] ?? '')?>">
      </td>

      <td>
        <form method="post" style="display:inline">
          <input type="hidden" name="sidang_akhir_id" value="<?= $row['sidang_akhir_id'] ?>">
          <button class="btn btn-sm btn-success" name="action" value="kirim_email">Kirim Email</button>
        </form>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>


<script>
document.addEventListener('DOMContentLoaded', function(){
  function showTempSuccess(el){
    el.classList.add('is-valid');
    setTimeout(function(){ el.classList.remove('is-valid'); }, 1200);
  }

  function saveInput(input){
    if (input.dataset.saving) return;
    input.dataset.saving = '1';
    var field = input.getAttribute('data-field');
    var id = input.getAttribute('data-id');
    var value = input.value;
    var fd = new FormData();
    fd.append('sidang_akhir_id', id);
    fd.append('field', field);
    fd.append('value', value);
    fd.append('action', 'save_field');

    fetch(location.href, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function(resp){
      return resp.json();
    }).then(function(data){
      if (data && data.status === 'ok') {
        showTempSuccess(input);
      } else {
        alert('Gagal menyimpan: ' + (data.message || 'error'));
      }
    }).catch(function(err){
      console.error(err);
      alert('Gagal menyimpan (network error).');
    }).finally(function(){ delete input.dataset.saving; });
  }

  document.querySelectorAll('.autosave').forEach(function(input){
    var tag = input.tagName.toLowerCase();
    if (tag === 'select') {
      input.addEventListener('change', function(){ saveInput(this); });
    } else {
      input.addEventListener('keydown', function(e){
        if (e.key === 'Enter') {
          e.preventDefault();
          saveInput(this);
        }
      });
      input.addEventListener('blur', function(e){
        saveInput(this);
      });
    }
  });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php';?>
