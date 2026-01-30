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
    $pendaftar_id = intval($_POST['pendaftar_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    // Save a single field (penguji_1, penguji_2, tanggal_sidang, jam_sidang, link_zoom, status)
    if ($action === 'save_field' && $pendaftar_id > 0 && isset($_POST['field']) && isset($_POST['value'])) {
        $allowed = ['penguji_1','penguji_2','tanggal_sidang','jam_sidang','link_zoom','status'];
        $field = $_POST['field'];
        $value = trim($_POST['value']);
        if (!in_array($field, $allowed)) {
            $error = 'Field tidak valid.';
        } else {
            // If status field, update user table instead
            if ($field === 'status') {
                $stmt = $mysqli->prepare('UPDATE user u JOIN pendaftaran p ON u.user_id = p.user_id SET u.status = ? WHERE p.pendaftar_id = ?');
                $stmt->bind_param('si', $value, $pendaftar_id);
                $stmt->execute();
            } else {
                // For other fields, check if penjadwalan exists
                $stmt = $mysqli->prepare('SELECT penjadwalan_id FROM penjadwalan WHERE pendaftaran_id = ? LIMIT 1');
                $stmt->bind_param('i', $pendaftar_id);
                $stmt->execute();
                $r = $stmt->get_result()->fetch_assoc();
                if ($r) {
                    $sql = "UPDATE penjadwalan SET {$field} = ? WHERE pendaftaran_id = ?";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param('si', $value, $pendaftar_id);
                    $stmt->execute();
                } else {
                    $sql = "INSERT INTO penjadwalan (pendaftaran_id, {$field}) VALUES (?, ?)";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param('is', $pendaftar_id, $value);
                    $stmt->execute();
                }
            }
            $info = 'Disimpan.';
            // Jika request AJAX, kembalikan JSON tanpa redirect
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'ok', 'message' => 'Disimpan']);
                exit;
            }
        }
        header('Location: penjadwalan.php'); exit;
    }

    // Kirim email notifikasi untuk baris ini
    if ($action === 'kirim_email' && $pendaftar_id > 0) {
        // pastikan data penjadwalan ada
        $stmt = $mysqli->prepare('SELECT p.pendaftar_id, p.nama_pendaftar, p.judul, p.pembimbing_lapangan, u.email, pen.tanggal_sidang, pen.jam_sidang, pen.link_zoom FROM pendaftaran p JOIN user u ON u.user_id = p.user_id LEFT JOIN penjadwalan pen ON pen.pendaftaran_id = p.pendaftar_id WHERE p.pendaftar_id = ? LIMIT 1');
        $stmt->bind_param('i', $pendaftar_id);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        if (!$r) { $error = 'Data pendaftar tidak ditemukan.'; }
        else {
            $display_date = !empty($r['tanggal_sidang']) ? (new DateTime($r['tanggal_sidang']))->format('d-m-Y') : '-';
            $display_time = '-';
            if (!empty($r['jam_sidang'])) {
                $t = DateTime::createFromFormat('H:i', $r['jam_sidang']);
                if (!$t) $t = DateTime::createFromFormat('H:i:s', $r['jam_sidang']);
                if ($t) $display_time = $t->format('H:i') . ' WIB';
            }
            $to = $r['email'];
            $subject = 'Informasi Penjadwalan Sidang PPI';
            $message = "Halo {$r['nama_pendaftar']},\n\nPenjadwalan sidang Anda telah diatur.\nPembimbing Lapangan: {$r['pembimbing_lapangan']}\nTanggal: {$display_date}\nJam: {$display_time}\nLink Zoom: {$r['link_zoom']}\n\nSalam.";
            $sent = send_email_smtp($to, $subject, $message);
            if ($sent) {
                $info = 'Email berhasil dikirim.';
            } else {
                $error = 'Gagal mengirim email (cek log).';
            }
        }
        header('Location: penjadwalan.php'); exit;
    }

    // verify / reject actions (keep existing behavior)
    if ($action === 'verify' && $pendaftar_id > 0) {
        $u = $mysqli->prepare('UPDATE pendaftaran p JOIN user u ON u.user_id = p.user_id SET p.status_pendaftaran = "diterima", u.status = "terverifikasi" WHERE p.pendaftar_id = ?');
        $u->bind_param('i', $pendaftar_id);
        $u->execute();
        header('Location: penjadwalan.php'); exit;
    }
    // Fungsi 'reject' telah dihapus dan tidak tersedia lagi
    

}

// Ambil pendaftar + penjadwalan (use u.status instead of separate status fields)
$res = $mysqli->query('SELECT p.*, u.email, u.status, pen.penguji_1, pen.penguji_2, pen.tanggal_sidang, pen.jam_sidang, pen.link_zoom FROM pendaftaran p JOIN user u ON u.user_id = p.user_id LEFT JOIN penjadwalan pen ON pen.pendaftaran_id = p.pendaftar_id ORDER BY p.created_at DESC');

require_once __DIR__ . '/../includes/header.php';
?>
<h2>Penjadwalan</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
<?php if (!empty($info)): ?><div class="alert alert-success"><?=htmlspecialchars($info)?></div><?php endif; ?>
<table class="table table-bordered table-sm">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nama</th>
      <th>Judul</th>
      <th>Dosen</th>
      <th>Pembimbing Lapangan</th>
      <th>Penguji 1</th>
      <th>Penguji 2</th>
      <th>Status</th>
      <th>Tanggal</th>
      <th>Jam</th>
      <th>Link Zoom</th>
      <th>Kirim Email</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($row = $res->fetch_assoc()): ?>
    <tr>
      <td><?= $row['pendaftar_id'] ?></td>
      <td><?= htmlspecialchars($row['nama_pendaftar']) ?></td>
      <td><?= htmlspecialchars($row['judul']) ?></td>
      <td><?= htmlspecialchars($row['nama_dosen']) ?></td>
      <td>
        <input type="text" class="form-control form-control-sm autosave" data-field="pembimbing_lapangan" data-id="<?= $row['pendaftar_id'] ?>" value="<?=htmlspecialchars($row['pembimbing_lapangan'] ?? '')?>" placeholder="Pembimbing Lapangan">
      </td>

      <td>
        <input type="text" class="form-control form-control-sm autosave" data-field="penguji_1" data-id="<?= $row['pendaftar_id'] ?>" value="<?=htmlspecialchars($row['penguji_1'] ?? '')?>" placeholder="Penguji 1">
      </td>

      <td>
        <input type="text" class="form-control form-control-sm autosave" data-field="penguji_2" data-id="<?= $row['pendaftar_id'] ?>" value="<?=htmlspecialchars($row['penguji_2'] ?? '')?>" placeholder="Penguji 2">
      </td>

      <td>
        <select class="form-select form-select-sm autosave" data-field="status" data-id="<?= $row['pendaftar_id'] ?>">
          <option value="belum_diverifikasi" <?= (isset($row['status']) && $row['status']==='belum_diverifikasi')? 'selected' : '' ?>>belum diverifikasi</option>
          <option value="terverifikasi" <?= (isset($row['status']) && $row['status']==='terverifikasi')? 'selected' : '' ?>>terverifikasi</option>
        </select>
      </td>

      <td>
        <input type="date" class="form-control form-control-sm autosave" data-field="tanggal_sidang" data-id="<?= $row['pendaftar_id'] ?>" value="<?=htmlspecialchars($row['tanggal_sidang'] ?? '')?>">
      </td>  

      <td>
        <input type="time" class="form-control form-control-sm autosave" data-field="jam_sidang" data-id="<?= $row['pendaftar_id'] ?>" value="<?=htmlspecialchars($row['jam_sidang'] ? (DateTime::createFromFormat('H:i:s', $row['jam_sidang']) ?: DateTime::createFromFormat('H:i', $row['jam_sidang']))->format('H:i') : '')?>">
      </td> 

      <td>
        <input type="text" class="form-control form-control-sm autosave" data-field="link_zoom" data-id="<?= $row['pendaftar_id'] ?>" value="<?=htmlspecialchars($row['link_zoom'] ?? '')?>" placeholder="Link Zoom">
      </td> 

      <td>
        <form method="post" style="display:inline">
          <input type="hidden" name="pendaftar_id" value="<?= $row['pendaftar_id'] ?>">
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
    fd.append('pendaftar_id', id);
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
