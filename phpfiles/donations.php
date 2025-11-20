<?php
require "db.php";
session_start();

// Only admins and partners can access
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'partner'])) {
    header("Location: login.php");
    exit;
}

require "dashboard_header.php";

// Fetch latest 200 donations
$res = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);

// Total donations
$total = $pdo->query("SELECT SUM(amount) as total FROM donations")->fetchColumn() ?? 0;

// Count of donations
$count = count($res);
?>
<h1>Donations</h1>
<div class="cards">
  <div class="card"><small>Total Donations</small><h3>$<?= number_format($total, 2) ?></h3></div>
  <div class="card"><small>Count</small><h3><?= $count ?></h3></div>
</div>

<table>
<thead>
<tr>
  <th>#</th>
  <th>Name</th>
  <th>Email</th>
  <th>Amount</th>
  <th>Message</th>
  <th>When</th>
</tr>
</thead>
<tbody>
<?php foreach($res as $d): ?>
  <tr>
    <td><?= $d['id'] ?></td>
    <td><?= htmlspecialchars($d['donor_name']) ?></td>
    <td><?= htmlspecialchars($d['email']) ?></td>
    <td>$<?= number_format($d['amount'], 2) ?></td>
    <td><?= htmlspecialchars($d['message']) ?></td>
    <td><?= $d['created_at'] ?></td>
  </tr>
<?php endforeach; ?>
</tbody>
</table>

<?php require "dashboard_footer.php"; ?>
