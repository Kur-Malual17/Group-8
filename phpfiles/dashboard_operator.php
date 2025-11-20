<?php
require "db.php";
session_start();

// Only operators can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'operator') {
    header("Location: login.php");
    exit;
}

require "dashboard_header.php";

$operator_id = $_SESSION['user']['id'];

try {
    // Recent reports
    $stmt = $pdo->prepare("
        SELECT * FROM boat_reports 
        WHERE operator_id = :operator_id 
        ORDER BY created_at DESC 
        LIMIT 30
    ");
    $stmt->execute([':operator_id' => $operator_id]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total reports
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM boat_reports WHERE operator_id = :operator_id");
    $stmt2->execute([':operator_id' => $operator_id]);
    $total_reports = (int)$stmt2->fetchColumn();

} catch (PDOException $e) {
    echo "<p style='color:red'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $res = [];
    $total_reports = 0;
}
?>

<h1>Boat Operator Dashboard</h1>

<div class="cards">
  <div class="card"><small>Your Reports</small><h3><?= $total_reports ?></h3></div>
</div>

<h2 style="margin-top:18px">Recent Reports</h2>
<table>
<thead>
<tr><th>#</th><th>Location</th><th>Fuel</th><th>Engine</th><th>Solar</th><th>Emergency</th><th>When</th></tr>
</thead>
<tbody>
<?php foreach($res as $r): ?>
  <tr>
    <td><?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['location']) ?></td>
    <td><?= htmlspecialchars($r['fuel_level']) ?></td>
    <td><?= htmlspecialchars($r['engine_status']) ?></td>
    <td><?= htmlspecialchars($r['solar_status']) ?></td>
    <td><?= htmlspecialchars($r['emergency']) ?></td>
    <td><?= $r['created_at'] ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php require "dashboard_footer.php"; ?>
