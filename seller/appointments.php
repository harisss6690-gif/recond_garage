<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

$stmt = $pdo->prepare("SELECT a.*, c.brand, c.model, c.year, 
                       u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone,
                       s.name AS assigned_seller_name
                       FROM appointments a 
                       JOIN cars c ON a.car_id = c.id 
                       JOIN users u ON a.customer_id = u.id 
                       LEFT JOIN users s ON a.assigned_seller_id = s.id
                       WHERE a.seller_id = ? OR a.assigned_seller_id = ?
                       ORDER BY a.created_at DESC");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointments - Seller</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h1>Appointment Requests</h1>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Appointment updated successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Appointment deleted successfully.</div>
    <?php endif; ?>

    <?php if (count($appointments) === 0): ?>
        <div class="alert alert-info">No appointment requests yet.</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>Salesperson</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['brand'] . ' ' . $a['model'] . ' (' . $a['year'] . ')') ?></td>
                    <td><?= htmlspecialchars($a['customer_name']) ?></td>
                    <td>
                        <?= htmlspecialchars($a['customer_email']) ?><br>
                        <small><?= htmlspecialchars($a['customer_phone'] ?? '-') ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($a['assigned_seller_name'] ?? '-') ?>
                        <?php if ($a['assigned_seller_id'] == $_SESSION['user_id']): ?>
                            <span class="badge bg-info">You</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $a['type'] ?></td>
                    <td><?= $a['preferred_date'] ?><br><small><?= $a['preferred_time'] ?></small></td>
                    <td>
                        <form method="POST" action="update_location.php" class="d-flex">
                            <input type="hidden" name="id" value="<?= $a['id'] ?>">
                            <input type="text" name="location" class="form-control form-control-sm me-1" 
                                   value="<?= htmlspecialchars($a['location'] ?? '') ?>" 
                                   placeholder="Set location">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                        </form>
                    </td>
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
                    <td>
                        <?php if ($a['status'] === 'Pending'): ?>
                            <a href="update_appointment.php?id=<?= $a['id'] ?>&action=approve" 
                               class="btn btn-sm btn-success"
                               onclick="return confirm('Approve this appointment?')">Approve</a>
                            <a href="update_appointment.php?id=<?= $a['id'] ?>&action=reject" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Reject this appointment?')">Reject</a>
                        <?php elseif ($a['status'] === 'Approved'): ?>
                            <a href="update_appointment.php?id=<?= $a['id'] ?>&action=complete" 
                               class="btn btn-sm btn-primary"
                               onclick="return confirm('Mark as completed?')">Complete</a>
                        <?php elseif (in_array($a['status'], ['Completed', 'Rejected', 'Cancelled'])): ?>
                            <a href="delete_appointment.php?id=<?= $a['id'] ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this appointment? This cannot be undone.')">Delete</a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
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