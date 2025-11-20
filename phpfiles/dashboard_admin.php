<?php
require "db.php";
session_start();

// Check admin login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require "dashboard_header.php";

// === Fetch counts with error handling ===
try {
    $total_patients = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM mcusers")->fetchColumn();
    $total_partners = $pdo->query("SELECT COUNT(*) FROM partners")->fetchColumn();
    $total_donations = $pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();

    // Recent patients
    $patients_res = $pdo->query("SELECT id, full_name, village, created_at FROM patients ORDER BY created_at DESC LIMIT 6")
                        ->fetchAll(PDO::FETCH_ASSOC);

    // Partners
    $partners_res = $pdo->query("SELECT * FROM partners ORDER BY created_at DESC LIMIT 20")
                        ->fetchAll(PDO::FETCH_ASSOC);

    // Users list
    $users_res = $pdo->query("SELECT id, full_name, email, role, created_at FROM mcusers ORDER BY created_at DESC LIMIT 50")
                     ->fetchAll(PDO::FETCH_ASSOC);

    // Donations for chart
    $donations_res = $pdo->query("
        SELECT DATE(created_at) as dt, SUM(amount) as total 
        FROM donations 
        GROUP BY DATE(created_at) 
        ORDER BY dt DESC 
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    $donations_labels = array_column($donations_res, 'dt');
    $donations_data = array_map('floatval', array_column($donations_res, 'total'));

} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}
?>
<h1>Admin Dashboard</h1>

<div class="cards">
  <div class="card"><small>Total Patients</small><h3><?= $total_patients ?></h3></div>
  <div class="card"><small>Total Users</small><h3><?= $total_users ?></h3></div>
  <div class="card"><small>Partners</small><h3><?= $total_partners ?></h3></div>
  <div class="card"><small>Donations</small><h3><?= $total_donations ?></h3></div>
</div>

<h2 style="margin-top:28px">Donations (recent)</h2>
<canvas id="donChart" style="max-width:700px"></canvas>

<h2 id="partners" style="margin-top:28px">Partners</h2>
<table>
  <thead>
    <tr><th>#</th><th>Name</th><th>Type</th><th>Email</th><th>Added</th><th>Action</th></tr>
  </thead>
  <tbody>
    <?php if(!empty($partners_res)): ?>
        <?php foreach($partners_res as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['partner_name']) ?></td>
                <td><?= htmlspecialchars($p['partner_type']) ?></td>
                <td><?= htmlspecialchars($p['contact_email']) ?></td>
                <td><?= $p['created_at'] ?></td>
                <td><a href="delete_partner.php?id=<?= $p['id'] ?>" onclick="return confirm('Delete partner?')">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="6">No partners found</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<h2 id="users" style="margin-top:28px">Users</h2>
<table>
  <thead>
    <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr>
  </thead>
  <tbody>
    <?php if(!empty($users_res)): ?>
        <?php foreach($users_res as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['full_name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td><?= $u['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="5">No users found</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<script>
const labels = <?= json_encode(array_reverse($donations_labels)) ?>;
const data = <?= json_encode(array_reverse($donations_data)) ?>;
const ctx = document.getElementById('donChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: labels,
    datasets: [{
      label: 'Donations (sum per day)',
      data: data,
      borderColor: 'rgba(30,144,255,0.9)',
      backgroundColor: 'rgba(0,168,107,0.12)',
      fill: true,
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});
</script>

<?php require "dashboard_footer.php"; ?>
