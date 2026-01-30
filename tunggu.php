<?php
require_once 'config.php';
if (!is_user_logged_in()) {
    header('Location: login.php');
    exit;
}
require_once 'includes/header.php';
?>
<h2>Terima Kasih</h2>
<p>Pendaftaran Anda telah dikirimkan. Silakan tunggu verifikasi oleh admin. Anda akan mendapatkan pemberitahuan jika pendaftaran disetujui dan dijadwalkan.</p>
<?php require_once 'includes/footer.php';?>