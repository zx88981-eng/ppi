<?php
// Database configuration
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'db_ppi';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: ' . $mysqli->connect_error);
}

session_start();

// Set timezone ke Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// PHPMailer autoload (di-install via Composer)
require_once __DIR__ . '/vendor/autoload.php';

// SMTP configuration - sesuaikan dengan akun SMTP Anda
$SMTP_HOST = 'smtp.example.com';
$SMTP_PORT = 587;
$SMTP_USER = 'user@example.com';
$SMTP_PASS = 'password';
$SMTP_SECURE = 'tls'; // tls or ssl
$SMTP_FROM = 'no-reply@example.com';
$SMTP_FROM_NAME = 'PPI System';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_email_smtp($to, $subject, $body, $altBody = '') {
    global $SMTP_HOST, $SMTP_PORT, $SMTP_USER, $SMTP_PASS, $SMTP_SECURE, $SMTP_FROM, $SMTP_FROM_NAME;
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = $SMTP_SECURE;
        $mail->Port = $SMTP_PORT;

        //Recipients
        $mail->setFrom($SMTP_FROM, $SMTP_FROM_NAME);
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body);
        $mail->AltBody = $altBody ?: strip_tags($body);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // log error jika diperlukan
        error_log('Mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

function is_admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function is_user_logged_in() {
    return !empty($_SESSION['user_id']);
}
?>