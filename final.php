<?php
require_once 'config.php';
if (!is_user_logged_in()) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
// Ambil penjadwalan untuk pendaftar user ini
$sql = "SELECT p.pendaftar_id, p.nama_pendaftar, p.judul, pen.tanggal_sidang, pen.jam_sidang, pen.link_zoom, pen.status_sidang
FROM pendaftaran p
LEFT JOIN penjadwalan pen ON pen.pendaftaran_id = p.pendaftar_id
WHERE p.user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();

require_once 'includes/header.php';
?>
<h2>Status Pendaftaran & Penjadwalan</h2>
<?php while ($r = $res->fetch_assoc()): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title"><?=htmlspecialchars($r['nama_pendaftar'])?> — <?=htmlspecialchars($r['judul'])?></h5>
      <p>Status: <strong><?=htmlspecialchars($r['status_sidang'] ?? 'belum terjadwal')?></strong></p>
      <?php if (!empty($r['tanggal_sidang'])): ?>
        <?php 
          $display_date = (new DateTime($r['tanggal_sidang']))->format('d-m-Y');
          $display_time = '-';
          if (!empty($r['jam_sidang'])) {
              $t = DateTime::createFromFormat('H:i', $r['jam_sidang']);
              if (!$t) $t = DateTime::createFromFormat('H:i:s', $r['jam_sidang']);
              if ($t) $display_time = $t->format('H:i') . ' WIB';
          }
        ?>
        <p>Tanggal: <?=htmlspecialchars($display_date)?> | Jam: <?=htmlspecialchars($display_time)?></p>
        <p>Link Zoom: <a href="<?=htmlspecialchars($r['link_zoom'])?>" target="_blank"><?=htmlspecialchars($r['link_zoom'])?></a></p>
      <?php else: ?>
        <p>Belum ada penjadwalan.</p>
      <?php endif; ?>
    </div>
  </div>
<?php endwhile; ?>
<?php require_once 'includes/footer.php';?>