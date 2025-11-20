<?php
require "db.php";
session_start();

// Only doctors and nurses can edit patients
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
$error = "";

try {
    // Fetch patient data
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        header("Location: dashboard_doctor.php");
        exit;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['full_name']);
        $age = (int)$_POST['age'];
        $gender = $_POST['gender'];
        $village = trim($_POST['village']);
        $diagnosis = trim($_POST['diagnosis']);
        $treatment = trim($_POST['treatment']);

        $update = $pdo->prepare("
            UPDATE patients
            SET full_name = :name,
                age = :age,
                gender = :gender,
                village = :village,
                diagnosis = :diagnosis,
                treatment = :treatment
            WHERE id = :id
        ");
        $update->execute([
            ':name' => $name,
            ':age' => $age,
            ':gender' => $gender,
            ':village' => $village,
            ':diagnosis' => $diagnosis,
            ':treatment' => $treatment,
            ':id' => $id
        ]);

        header("Location: dashboard_doctor.php");
        exit;
    }

} catch (PDOException $e) {
    $error = $e->getMessage();
}

require "dashboard_header.php";
?>

<h1>Edit Patient</h1>
<?php if($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
  <input name="full_name" value="<?= htmlspecialchars($row['full_name']) ?>" required>
  <input name="age" type="number" value="<?= htmlspecialchars($row['age']) ?>">
  <select name="gender" required>
    <option value="">Gender</option>
    <option <?= $row['gender']=='Male'?'selected':'' ?>>Male</option>
    <option <?= $row['gender']=='Female'?'selected':'' ?>>Female</option>
    <option <?= $row['gender']=='Other'?'selected':'' ?>>Other</option>
  </select>
  <input name="village" value="<?= htmlspecialchars($row['village']) ?>">
  <textarea name="diagnosis"><?= htmlspecialchars($row['diagnosis']) ?></textarea>
  <textarea name="treatment"><?= htmlspecialchars($row['treatment']) ?></textarea>
  <button type="submit">Update</button>
</form>

<?php require "dashboard_footer.php"; ?>
