<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = mysqli_query($conn, "SELECT * FROM mcusers WHERE email='$email' LIMIT 1");

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user["password"])) {
            $_SESSION["user"] = $user;

            if ($user["role"] == "admin") {
                header("Location: dashboard_admin.php");
            }
            elseif ($user["role"] == "doctor" || $user["role"] == "nurse") {
                header("Location: dashboard_doctor.php");
            }
            elseif ($user["role"] == "operator") {
                header("Location: dashboard_operator.php");
            }
            elseif ($user["role"] == "partner") {
                header("Location: dashboard_partner.php");
            }
            exit;
        } else {
            $error = "Incorrect password.";
        }

    } else {
        $error = "No account found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - MobileCare</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

<div class="content">
    <h2>Login</h2>

    <?php if(isset($error)): ?>
    <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" class="contact-form">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

</div>

</body>
</html>
