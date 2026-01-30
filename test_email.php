<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? 'Test Email';
    $message = $_POST['message'] ?? 'Tes pengiriman email via SMTP.';
    $sent = send_email_smtp($to, $subject, $message);
    $status = $sent ? 'Terkirim' : 'Gagal mengirim (cek log)';
}
require_once 'includes/header.php';
?>
<h2>Test SMTP Email</h2>
<?php if (!empty($status)): ?>
  <div class="alert alert-info"><?=htmlspecialchars($status)?></div>
<?php endif; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">To (email)</label><input name="to" type="email" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Subject</label><input name="subject" class="form-control" value="Test Email"></div>
  <div class="mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control">Tes pengiriman email via SMTP.</textarea></div>
  <button class="btn btn-primary">Kirim Test</button>
</form>
<?php require_once 'includes/footer.php';?>