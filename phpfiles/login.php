<?php
session_start();
require "db.php";

$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Fetch user using PDO
    $stmt = $pdo->prepare("SELECT id, full_name, email, password, role FROM mcusers WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u) {
        if (password_verify($password, $u['password'])) {
            $_SESSION['user'] = $u;

            switch ($u['role']) {
                case 'admin': header("Location: dashboard_admin.php"); break;
                case 'operator': header("Location: dashboard_operator.php"); break;
                case 'partner': header("Location: dashboard_partner.php"); break;
                case 'doctor':
                case 'nurse': header("Location: dashboard_doctor.php"); break;
                default: header("Location: dashboard_doctor.php");
            }
            exit;
        } else {
            $err = "Incorrect password.";
        }
    } else {
        $err = "User not found.";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - MobileCare</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="content" style="max-width:400px; margin:60px auto; text-align:left;">
  <h2>Login</h2>
  <?php if($err): ?><p style="color:red"><?= htmlspecialchars($err) ?></p><?php endif; ?>
  <form method="post" class="contact-form">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <p style="margin-top:12px">No account? <a href="register.php">Register</a></p>
  <p><a href="index.html">← Back to site</a></p>
</div>
</body>
</html>
