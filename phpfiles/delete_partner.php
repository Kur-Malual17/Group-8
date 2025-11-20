<?php
require "db.php";
session_start();

// Only admins can delete partners
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM partners WHERE id = :id");
    $stmt->execute([':id' => $id]);
} catch (PDOException $e) {
    // Optionally, log the error somewhere instead of showing it
    // error_log($e->getMessage());
    header("Location: dashboard_admin.php?error=delete_failed");
    exit;
}

// Redirect back to dashboard
header("Location: dashboard_admin.php");
exit;
