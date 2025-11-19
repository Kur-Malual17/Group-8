<?php
$host = "localhost";
$user = "kur.malual";
$pass = "kur_799";
$dbname = "webtech_2025A_kur_malual";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
