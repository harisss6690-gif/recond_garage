<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('admin');

$stmt = $pdo->query("SELECT a.*, 
                     c.brand, c.model, c.year, 
                     cu.name AS customer_name, cu.email AS customer_email, cu.phone AS customer_phone,
                     su.name AS seller_name, su.email AS seller_email 
                     FROM appointments a 
                     JOIN cars c ON a.car_id = c.id 
                     JOIN users cu ON a.customer_id = cu.id 
                     JOIN users su ON a.seller_id = su.id 
                     ORDER BY a.created_at DESC");
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Appointments - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h1>All Appointments (<?= count($appointments) ?>)</h1>

    <?php if (count($appointments) === 0): ?>
        <div class="alert alert-info">No appointments yet.</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Customer</th>
                    <th>Seller</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['brand'] . ' ' . $a['model'] . ' (' . $a['year'] . ')') ?></td>
                    <td>
                        <?= htmlspecialchars($a['customer_name']) ?><br>
                        <small><?= htmlspecialchars($a['customer_email']) ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($a['seller_name']) ?><br>
                        <small><?= htmlspecialchars($a['seller_email']) ?></small>
                    </td>
                    <td><?= $a['type'] ?></td>
                    <td><?= $a['preferred_date'] ?> <?= $a['preferred_time'] ?></td>
                    <td>
                        <?php
                        $colors = [
                            'Pending' => 'warning',
                            'Approved' => 'success',
                            'Rejected' => 'danger',
                            'Rescheduled' => 'info',
                            'Completed' => 'primary',
                            'Cancelled' => 'secondary'
                        ];
                        $color = $colors[$a['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $color ?>"><?= $a['status'] ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-link mt-3">&larr; Back to Dashboard</a>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>