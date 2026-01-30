<?php
require 'config.php';
$res = $mysqli->query("SELECT admin_id, email, nama, password, role FROM admin");
while ($r = $res->fetch_assoc()) {
    echo implode('|', [$r['admin_id'], $r['email'], $r['nama'], $r['password'], $r['role']]) . "\n";
}
?>