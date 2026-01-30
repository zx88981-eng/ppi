<?php
require_once __DIR__ . '/../config.php';
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}
$q = $_GET['q'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_dosen'] ?? '';
    $bidang = $_POST['bidang_keahlian'] ?? '';
    $stmt = $mysqli->prepare('INSERT INTO dosen (nama_dosen, bidang_keahlian) VALUES (?, ?)');
    $stmt->bind_param('ss', $nama, $bidang);
    $stmt->execute();
    header('Location: dosen.php');
    exit;
}
if (!empty($q)) {
    $stmt = $mysqli->prepare('SELECT * FROM dosen WHERE nama_dosen LIKE ? ORDER BY nama_dosen');
    $like = "%$q%";
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $mysqli->query('SELECT * FROM dosen ORDER BY nama_dosen');
}
require_once __DIR__ . '/../includes/header.php';
?>
<h2>List Dosen</h2>
<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input type="text" name="q" value="<?=htmlspecialchars($q)?>" class="form-control" placeholder="Cari dosen...">
  </div>
  <div class="col-auto"><button class="btn btn-secondary">Search</button></div>
</form>
<table class="table table-bordered">
  <thead><tr><th>ID</th><th>Nama</th><th>Bidang</th></tr></thead>
  <tbody>
  <?php while ($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?=htmlspecialchars($r['dosen_id'])?></td>
      <td><?=htmlspecialchars($r['nama_dosen'])?></td>
      <td><?=htmlspecialchars($r['bidang_keahlian'])?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<hr>
<h4>Tambah Dosen</h4>
<form method="post">
  <div class="mb-3">
    <label class="form-label">Nama Dosen</label>
    <input type="text" name="nama_dosen" required class="form-control">
  </div>
  <div class="mb-3">
    <label class="form-label">Bidang Keahlian</label>
    <input type="text" name="bidang_keahlian" class="form-control">
  </div>
  <button class="btn btn-primary">Simpan</button>
</form>
<?php require_once __DIR__ . '/../includes/footer.php';?>