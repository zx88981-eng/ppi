<?php
require_once __DIR__ . '/../config.php';
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Dashboard Admin</h2>
<div class="list-group">
  <a class="list-group-item list-group-item-action" href="dosen.php">List Dosen</a>
  <a class="list-group-item list-group-item-action" href="users.php">List User</a>
  <a class="list-group-item list-group-item-action" href="penjadwalan.php">Penjadwalan</a>
  <a class="list-group-item list-group-item-action" href="penilaian.php">Penilaian</a>
  <a class="list-group-item list-group-item-action" href="sidang_akhir.php">Sidang Akhir</a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php';?>
