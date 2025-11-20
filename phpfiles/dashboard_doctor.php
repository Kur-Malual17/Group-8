<?php
require "db.php";
session_start();

// Only doctors and nurses can access
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['doctor','nurse'])) {
    header("Location: login.php");
    exit;
}

require "dashboard_header.php";

try {
    // Total patients
    $total_patients = (int)$pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();

    // Last 12 patients with creator info
    $p_res = $pdo->query("
        SELECT p.id, p.full_name, p.village, p.created_at, u.full_name AS created_by_name
        FROM patients p
        LEFT JOIN mcusers u ON p.created_by = u.id
        ORDER BY p.created_at DESC
        LIMIT 12
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Patients per day (for chart)
    $perday = $pdo->query("
        SELECT DATE(created_at) as dt, COUNT(*) as c
        FROM patients
        GROUP BY DATE(created_at)
        ORDER BY dt DESC
        LIMIT 7
    ")->fetchAll(PDO::FETCH_ASSOC);

    $labels = array_column($perday, 'dt');
    $vals = array_map('intval', array_column($perday, 'c'));

} catch (PDOException $e) {
    echo "<p style='color:red'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    $p_res = [];
    $labels = [];
    $vals = [];
    $total_patients = 0;
}
?>

<h1>Medical Dashboard</h1>

<div class="cards">
  <div class="card"><small>Total Patients</small><h3><?= $total_patients ?></h3></div>
  <div class="card"><small>Your Role</small><h3><?= ucfirst($_SESSION['user']['role']) ?></h3></div>
</div>

<h2 style="margin-top:20px">New Patients (recent)</h2>
<table>
  <thead>
    <tr><th>#</th><th>Name</th><th>Village</th><th>Added by</th><th>Added</th><th>Action</th></tr>
  </thead>
  <tbody>
    <?php foreach($p_res as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['full_name']) ?></td>
        <td><?= htmlspecialchars($p['village']) ?></td>
        <td><?= htmlspecialchars($p['created_by_name'] ?? '') ?></td>
        <td><?= $p['created_at'] ?></td>
        <td>
          <a href="edit_patient.php?id=<?= $p['id'] ?>">Edit</a> |
          <a href="delete_patient.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete patient?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<h2 style="margin-top:28px">Patients by day</h2>
<canvas id="patientChart" style="max-width:700px"></canvas>

<script>
const lbls = <?= json_encode(array_reverse($labels)) ?>;
const vals = <?= json_encode(array_reverse($vals)) ?>;
const ctx = document.getElementById('patientChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: lbls,
        datasets: [{
            label: 'Patients',
            data: vals,
            backgroundColor: 'rgba(30,144,255,0.6)'
        }]
    },
    options: { responsive: true }
});
</script>

<?php require "dashboard_footer.php"; ?>
