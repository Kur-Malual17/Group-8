<?php
require "db.php";
session_start();

// Only boat operators can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'operator') {
    header("Location: login.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = trim($_POST['location']);
    $fuel_level = trim($_POST['fuel_level']);
    $engine_status = trim($_POST['engine_status']);
    $solar_status = trim($_POST['solar_status']);
    $weather = trim($_POST['weather']);
    $emergency = trim($_POST['emergency']);
    $operator_id = $_SESSION['user']['id'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO boat_reports 
            (operator_id, location, fuel_level, engine_status, solar_status, weather, emergency) 
            VALUES 
            (:operator_id, :location, :fuel_level, :engine_status, :solar_status, :weather, :emergency)
        ");
        $stmt->execute([
            ':operator_id' => $operator_id,
            ':location' => $location,
            ':fuel_level' => $fuel_level,
            ':engine_status' => $engine_status,
            ':solar_status' => $solar_status,
            ':weather' => $weather,
            ':emergency' => $emergency
        ]);

        header("Location: dashboard_operator.php");
        exit;
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

require "dashboard_header.php";
?>

<h1>Submit Boat Report</h1>
<?php if ($error) echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>"; ?>

<form method="post">
  <input name="location" placeholder="Location (village / coords)" required>
  <input name="fuel_level" placeholder="Fuel level (e.g., Full / 75%)">
  <input name="engine_status" placeholder="Engine status">
  <input name="solar_status" placeholder="Solar status">
  <textarea name="weather" placeholder="Weather / flood notes"></textarea>
  <textarea name="emergency" placeholder="Emergency notes (if any)"></textarea>
  <button type="submit">Submit Report</button>
</form>

<?php require "dashboard_footer.php"; ?>
