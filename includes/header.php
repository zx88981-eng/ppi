<?php
require_once __DIR__ . '/../config.php';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PPI - Sistem</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/ppi">PPI</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (is_admin_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="/ppi/admin/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="/ppi/logout.php">Logout</a></li>
        <?php elseif (is_user_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="/ppi/pendaftaran.php">Pendaftaran</a></li>
          <li class="nav-item"><a class="nav-link" href="/ppi/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/ppi/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/ppi/registrasi.php">Registrasi</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">