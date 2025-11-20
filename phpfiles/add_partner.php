<?php
require "db.php";
session_start();

// Only admins can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partner_name = trim($_POST['partner_name']);
    $partner_type = $_POST['partner_type'];
    $contact_email = trim($_POST['contact_email']);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO partners (partner_name, partner_type, contact_email)
            VALUES (:partner_name, :partner_type, :contact_email)
        ");
        $stmt->execute([
            ':partner_name' => $partner_name,
            ':partner_type' => $partner_type,
            ':contact_email' => $contact_email
        ]);
        $success = "Partner added successfully!";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

require "dashboard_header.php";
?>

<h1>Add Partner</h1>

<?php 
if (!empty($error)) echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>";
if (!empty($success)) echo "<p style='color:green'>" . htmlspecialchars($success) . "</p>";
?>

<form method="post">
  <input name="partner_name" placeholder="Partner name" required>
  <select name="partner_type" required>
    <option value="">Select type</option>
    <option value="hospital">Hospital</option>
    <option value="clinic">Clinic</option>
    <option value="organization">Organization</option>
    <option value="csr_company">CSR / Company</option>
  </select>
  <input name="contact_email" placeholder="Contact email" type="email" required>
  <button type="submit">Add Partner</button>
</form>

<?php require "dashboard_footer.php"; ?>
