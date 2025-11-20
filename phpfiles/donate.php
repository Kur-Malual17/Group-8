<?php
require "db.php";

$err = "";
$ok = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['donor_name']);
    $email = trim($_POST['email']);
    $amount = (float)$_POST['amount'];
    $message = trim($_POST['message']);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO donations (donor_name, email, amount, message)
            VALUES (:donor_name, :email, :amount, :message)
        ");
        $stmt->execute([
            ':donor_name' => $name,
            ':email' => $email,
            ':amount' => $amount,
            ':message' => $message
        ]);
        $ok = "Thank you — donation recorded. (In production integrate payment gateway)";
    } catch (PDOException $e) {
        $err = "Database error: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Donate - MobileCare</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header style="background:var(--blue); padding:12px; color:white;">
  <div style="max-width:1000px;margin:auto">
    <a href="index.html" style="color:white;text-decoration:none">← Back to site</a>
  </div>
</header>

<main style="max-width:720px;margin:30px auto">
  <h1>Donate Now</h1>
  <?php if ($err) echo "<p style='color:red'>" . htmlspecialchars($err) . "</p>"; ?>
  <?php if ($ok) echo "<p style='color:green'>" . htmlspecialchars($ok) . "</p>"; ?>
  <form method="post" class="contact-form">
    <input name="donor_name" placeholder="Full name" required>
    <input name="email" type="email" placeholder="Email" required>
    <input name="amount" type="number" step="0.01" placeholder="Amount (USD)" required>
    <textarea name="message" placeholder="Message / purpose (optional)"></textarea>
    <button type="submit">Record Donation</button>
  </form>
  <p style="margin-top:12px">
    Note: this form records a donation in the database. To accept real payments, integrate a gateway (Paystack/Flutterwave/Stripe) and replace the form action with gateway flow.
  </p>
</main>
</body>
</html>
