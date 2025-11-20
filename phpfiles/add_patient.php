<?php
require "db.php";
session_start();

// Only doctors and nurses can access
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['doctor','nurse'])) {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $village = trim($_POST['village']);
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);
    $created_by = $_SESSION['user']['id'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO patients (full_name, age, gender, village, diagnosis, treatment, created_by)
            VALUES (:full_name, :age, :gender, :village, :diagnosis, :treatment, :created_by)
        ");
        $stmt->execute([
            ':full_name' => $name,
            ':age' => $age,
            ':gender' => $gender,
            ':village' => $village,
            ':diagnosis' => $diagnosis,
            ':treatment' => $treatment,
            ':created_by' => $created_by
        ]);
        header("Location: dashboard_doctor.php");
        exit;
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

require "dashboard_header.php";
?>

<h1>Add Patient</h1>
<?php if ($error) echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>"; ?>

<form method="post">
  <input name="full_name" placeholder="Full name" required>
  <input name="age" type="number" placeholder="Age">
  <select name="gender" required>
    <option value="">Gender</option>
    <option>Male</option>
    <option>Female</option>
    <option>Other</option>
  </select>
  <input name="village" placeholder="Village">
  <textarea name="diagnosis" placeholder="Diagnosis"></textarea>
  <textarea name="treatment" placeholder="Treatment"></textarea>
  <button type="submit">Save Patient</button>
</form>

<?php require "dashboard_footer.php"; ?>
