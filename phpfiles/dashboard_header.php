<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$roleSafe = htmlspecialchars($user['role']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= ucfirst($roleSafe) ?> Dashboard - MobileCare</title>
  <link rel="stylesheet" href="dashboard_styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="dashboard-container">
  <div class="sidebar">
    <h2>MobileCare</h2>

    <?php if ($user['role'] === 'admin'): ?>
      <a href="dashboard_admin.php" class="active">Dashboard</a>
      <a href="add_partner.php">Add Partner</a>
      <a href="dashboard_admin.php#partners">View Partners</a>
      <a href="dashboard_admin.php#users">Manage Users</a>
      <a href="donations.php">Donations</a>
    <?php endif; ?>

    <?php if (in_array($user['role'], ['doctor','nurse'])): ?>
      <a href="dashboard_doctor.php" class="active">Dashboard</a>
      <a href="add_patient.php">Add Patient</a>
    <?php endif; ?>

    <?php if ($user['role'] === 'operator'): ?>
      <a href="dashboard_operator.php" class="active">Dashboard</a>
      <a href="boat_report.php">Submit Report</a>
    <?php endif; ?>

    <?php if ($user['role'] === 'partner'): ?>
      <a href="dashboard_partner.php" class="active">Dashboard</a>
      <a href="donations.php">Donation Transparency</a>
    <?php endif; ?>

    <a href="logout.php" class="logout-btn">Logout</a>
  </div>

  <div class="main-content">
