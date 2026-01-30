<?php
require_once 'config.php';
if (!is_user_logged_in()) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$user_status = $_SESSION['user_status'] ?? '';

// Cek apakah user sudah terverifikasi
if ($user_status !== 'terverifikasi') {
    header('Location: tunggu.php');
    exit;
}

// Ambil penjadwalan untuk pendaftar user ini
$sql = "SELECT p.pendaftar_id, p.nama_pendaftar, p.judul, p.pembimbing_lapangan, p.nama_dosen, pen.tanggal_sidang, pen.jam_sidang, pen.link_zoom, pen.penguji_1, pen.penguji_2
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
<div class="alert alert-info">
  <strong>Selamat!</strong> Pendaftaran Anda telah diverifikasi oleh admin. Berikut adalah informasi penjadwalan sidang Anda:
</div>
<?php while ($r = $res->fetch_assoc()): ?>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title"><?=htmlspecialchars($r['nama_pendaftar'])?></h5>
      <p><strong>Judul:</strong> <?=htmlspecialchars($r['judul'])?></p>
      <p><strong>Dosen Pembimbing:</strong> <?=htmlspecialchars($r['nama_dosen'])?></p>
      <p><strong>Pembimbing Lapangan:</strong> <?=htmlspecialchars($r['pembimbing_lapangan'])?></p>
      
      <hr>
      
      <h6>Informasi Penjadwalan Sidang</h6>
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
        <div class="alert alert-success">
          <p><strong>Tanggal Sidang:</strong> <?=htmlspecialchars($display_date)?></p>
          <p><strong>Jam Sidang:</strong> <?=htmlspecialchars($display_time)?></p>
          <?php if (!empty($r['link_zoom'])): ?>
            <p><strong>Link Zoom:</strong> <a href="<?=htmlspecialchars($r['link_zoom'])?>" target="_blank"><?=htmlspecialchars($r['link_zoom'])?></a></p>
          <?php endif; ?>
          <?php if (!empty($r['penguji_1'])): ?>
            <p><strong>Penguji 1:</strong> <?=htmlspecialchars($r['penguji_1'])?></p>
          <?php endif; ?>
          <?php if (!empty($r['penguji_2'])): ?>
            <p><strong>Penguji 2:</strong> <?=htmlspecialchars($r['penguji_2'])?></p>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-warning">
          <p>Jadwal sidang belum diatur. Silakan tunggu pemberitahuan lebih lanjut dari admin.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
<?php endwhile; ?>
<?php require_once 'includes/footer.php';?>