<?php
require "db.php";
session_start();

// Only doctors and nurses can delete patients
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['doctor','nurse'])) {
    header("Location: login.php");
    exit;
}

// Check for patient ID
if (!isset($_GET['id'])) {
    header("Location: dashboard_doctor.php");
    exit;
}

$id = (int)$_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
    $stmt->execute([':id' => $id]);
} catch (PDOException $e) {
    echo "<p style='color:red'>Error deleting patient: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

header("Location: dashboard_doctor.php");
exit;
