<?php
require 'config.php';
$identifier = 'simt.its.ac.id';
$password = '123';
$stmt = $mysqli->prepare('SELECT admin_id, nama, password FROM admin WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $identifier);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $row = $res->fetch_assoc()) {
    if ($row['password'] === $password) {
        echo "Admin login OK: {$row['nama']}\n";
    } else {
        echo "Admin password mismatch\n";
    }
} else {
    echo "Admin not found\n";
}

// Test user lookup by nrp
$identifier2 = '135792';
$password2 = 'pass';
$stmt = $mysqli->prepare('SELECT user_id, nama_lengkap, password FROM user WHERE nrp = ? LIMIT 1');
$stmt->bind_param('s', $identifier2);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $row = $res->fetch_assoc()) {
    if ($row['password'] === $password2) {
        echo "User login OK: {$row['nama_lengkap']}\n";
    } else {
        echo "User password mismatch\n";
    }
} else {
    echo "User not found\n";
}
?>