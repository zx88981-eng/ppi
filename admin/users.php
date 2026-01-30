<?php
require_once __DIR__ . '/../config.php';
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}
$q = $_GET['q'] ?? '';
if (!empty($q)) {
    $stmt = $mysqli->prepare('SELECT * FROM user WHERE nama_lengkap LIKE ? OR email LIKE ? OR nrp LIKE ? ORDER BY created_at DESC');
    $like = "%$q%";
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $mysqli->query('SELECT * FROM user ORDER BY created_at DESC');
}
require_once __DIR__ . '/../includes/header.php';
?>
<h2>List User</h2>
<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input type="text" name="q" value="<?=htmlspecialchars($q)?>" class="form-control" placeholder="Cari user...">
  </div>
  <div class="col-auto"><button class="btn btn-secondary">Search</button></div>
</form>
<table class="table table-bordered">
  <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>NRP</th><th>Password (plain)</th><th>Status</th></tr></thead>
  <tbody>
  <?php while ($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?=htmlspecialchars($r['user_id'])?></td>
      <td><?=htmlspecialchars($r['nama_lengkap'])?></td>
      <td><?=htmlspecialchars($r['email'])?></td>
      <td><?=htmlspecialchars($r['nrp'])?></td>
      <td><?=htmlspecialchars($r['password'])?></td>
      <td><?=htmlspecialchars($r['status'])?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php';?>