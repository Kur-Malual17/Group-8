<?php
$servername = "localhost";
$username = "kur.malual";
$password = "kur_799";
$dbname = "webtech_2025A_kur_malual";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ ERROR: Could not connect. " . $e->getMessage());
}
?>
