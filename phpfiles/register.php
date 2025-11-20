<?php
require "db.php";

$err = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    $valid_roles = ['admin', 'doctor', 'nurse', 'operator', 'partner'];
    if (!in_array($role, $valid_roles)) {
        $err = "Invalid role.";
    } else {
        $check = $pdo->prepare("SELECT email FROM mcusers WHERE email = :email");
        $check->execute([':email' => $email]);

        if ($check->rowCount() > 0) {
            $err = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO mcusers (full_name, email, password, role)
                VALUES (:full_name, :email, :password, :role)
            ");

            $params = [
                ':full_name' => $full_name,
                ':email'     => $email,
                ':password'  => $hash,
                ':role'      => $role
            ];

            if ($stmt->execute($params)) {
                $success = "Account created successfully!";
            } else {
                $err = "Database error. Try again.";
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Register - MobileCare</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="content" style="max-width:480px; margin:60px auto; text-align:left;">
  <h2>Register (Doctor / Nurse / Operator / Partner / Admin)</h2>

  <?php if($err): ?><p style="color:red"><?= htmlspecialchars($err) ?></p><?php endif; ?>
  <?php if($success): ?><p style="color:green"><?= htmlspecialchars($success) ?></p><?php endif; ?>

  <form method="post" class="contact-form">
      <input name="full_name" placeholder="Full name" required>
      <input name="email" type="email" placeholder="Email" required>
      <select name="role" required>
        <option value="">Select role</option>
        <option value="admin">Admin</option>
        <option value="doctor">Doctor</option>
        <option value="nurse">Nurse</option>
        <option value="operator">Boat Operator</option>
        <option value="partner">Partner</option>
      </select>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Create Account</button>
  </form>

  <p style="margin-top:12px"><a href="login.php">← Back to login</a></p>
</div>
</body>
</html>
