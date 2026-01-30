<?php
require_once __DIR__ . '/../config.php';
if (!is_admin_logged_in()) {
    header('Location: ../login.php');
    exit;
}
// list pendaftar
$res = $mysqli->query('SELECT p.*, u.nama_lengkap, pen.nilai_akhir FROM pendaftaran p JOIN user u ON u.user_id = p.user_id LEFT JOIN penilaian pen ON pen.pendaftar_id = p.pendaftar_id ORDER BY p.created_at DESC');
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Penilaian</h2>
<table class="table table-bordered">
  <thead><tr><th>ID</th><th>Nama</th><th>Judul</th><th>Proposal</th><th>Aksi</th><th>Nilai Akhir</th></tr></thead>
  <tbody>
  <?php while ($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?= $r['pendaftar_id'] ?></td>
      <td><?= htmlspecialchars($r['nama_lengkap']) ?></td>
      <td><?= htmlspecialchars($r['judul']) ?></td>
      <td><?php if ($r['file_proposal']): ?><a href="/ppi/<?=htmlspecialchars($r['file_proposal'])?>" target="_blank"><?=htmlspecialchars(basename($r['file_proposal']))?></a><?php else: ?>-<?php endif; ?></td>
      <td>
        <a class="btn btn-sm btn-primary" href="penilaian_edit.php?pendaftar_id=<?= $r['pendaftar_id'] ?>">Nilai</a>
      </td>
      <td><?= htmlspecialchars($r['nilai_akhir'] ?? '-') ?></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php';?>