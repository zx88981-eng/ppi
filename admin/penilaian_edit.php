<?php
require_once __DIR__ . '/../config.php';
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}
$pendaftar_id = intval($_GET['pendaftar_id'] ?? 0);
if ($pendaftar_id <= 0) {
    header('Location: penilaian.php');
    exit;
}
// ambil data pendaftar
$stmt = $mysqli->prepare('SELECT p.*, u.nama_lengkap FROM pendaftaran p JOIN user u ON u.user_id = p.user_id WHERE p.pendaftar_id = ? LIMIT 1');
$stmt->bind_param('i', $pendaftar_id);
$stmt->execute();
$pend = $stmt->get_result()->fetch_assoc();

// ambil/siapkan penilaian
$stmt = $mysqli->prepare('SELECT * FROM penilaian WHERE pendaftar_id = ? LIMIT 1');
$stmt->bind_param('i', $pendaftar_id);
$stmt->execute();
$pen = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n1 = floatval($_POST['nilai_pembimbing1'] ?? 0);
    $n2 = floatval($_POST['nilai_pembimbing2'] ?? 0);
    $n3 = floatval($_POST['nilai_penguji1'] ?? 0);
    $n4 = floatval($_POST['nilai_penguji2'] ?? 0);
    $total = $n1 + $n2 + $n3 + $n4;

    if ($pen) {
        $stmt = $mysqli->prepare('UPDATE penilaian SET nilai_pembimbing1=?, nilai_pembimbing2=?, nilai_penguji1=?, nilai_penguji2=?, nilai_akhir=? WHERE pendaftar_id=?');
        $stmt->bind_param('dddddi', $n1, $n2, $n3, $n4, $total, $pendaftar_id);
        $stmt->execute();
    } else {
        $stmt = $mysqli->prepare('INSERT INTO penilaian (pendaftar_id, nilai_pembimbing1, nilai_pembimbing2, nilai_penguji1, nilai_penguji2, nilai_akhir) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iddddd', $pendaftar_id, $n1, $n2, $n3, $n4, $total);
        $stmt->execute();
    }
    header('Location: penilaian.php');
    exit;
}
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Penilaian: <?=htmlspecialchars($pend['nama_lengkap'])?> — <?=htmlspecialchars($pend['judul'])?></h2>
<form method="post">
  <div class="mb-3">
    <label class="form-label">Nilai Pembimbing 1</label>
    <input type="number" step="0.1" name="nilai_pembimbing1" class="form-control nilai-input" value="<?=htmlspecialchars($pen['nilai_pembimbing1'] ?? 0)?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Nilai Pembimbing 2</label>
    <input type="number" step="0.1" name="nilai_pembimbing2" class="form-control nilai-input" value="<?=htmlspecialchars($pen['nilai_pembimbing2'] ?? 0)?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Nilai Penguji 1</label>
    <input type="number" step="0.1" name="nilai_penguji1" class="form-control nilai-input" value="<?=htmlspecialchars($pen['nilai_penguji1'] ?? 0)?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Nilai Penguji 2</label>
    <input type="number" step="0.1" name="nilai_penguji2" class="form-control nilai-input" value="<?=htmlspecialchars($pen['nilai_penguji2'] ?? 0)?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Total (otomatis jumlah)</label>
    <input type="number" step="0.1" readonly class="form-control" id="total-nilai" value="<?=htmlspecialchars($pen['nilai_akhir'] ?? 0)?>">
  </div>
  <button class="btn btn-primary">Simpan</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function(){
  function hitungTotal(){
    var n1 = parseFloat(document.querySelector('input[name="nilai_pembimbing1"]').value) || 0;
    var n2 = parseFloat(document.querySelector('input[name="nilai_pembimbing2"]').value) || 0;
    var n3 = parseFloat(document.querySelector('input[name="nilai_penguji1"]').value) || 0;
    var n4 = parseFloat(document.querySelector('input[name="nilai_penguji2"]').value) || 0;
    var total = n1 + n2 + n3 + n4;
    document.getElementById('total-nilai').value = total;
  }
  
  document.querySelectorAll('.nilai-input').forEach(function(input){
    input.addEventListener('input', hitungTotal);
  });
  
  hitungTotal();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php';?>