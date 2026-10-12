<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('customer');

// Stats
$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE customer_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalAppointments = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE customer_id = ? AND status = 'Pending'");
$stmt->execute([$_SESSION['user_id']]);
$pendingCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE customer_id = ? AND status = 'Approved'");
$stmt->execute([$_SESSION['user_id']]);
$approvedCount = $stmt->fetchColumn();

// Recent appointments
$stmt = $pdo->prepare("SELECT a.*, c.brand, c.model, c.year 
                       FROM appointments a 
                       JOIN cars c ON a.car_id = c.id 
                       WHERE a.customer_id = ? 
                       ORDER BY a.created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recent = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Dashboard - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
    <p class="text-muted">Your personal dashboard</p>

    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Appointments</h5>
                    <h2><?= $totalAppointments ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h2><?= $pendingCount ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <h2><?= $approvedCount ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-8">
            <h4>Recent Appointments</h4>
            <?php if (count($recent) === 0): ?>
                <div class="alert alert-info">
                    You have no appointments yet. <a href="/recond_garage/pages/listings.php">Browse cars</a> to book one.
                </div>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Car</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['brand'] . ' ' . $a['model'] . ' (' . $a['year'] . ')') ?></td>
                            <td><?= $a['type'] ?></td>
                            <td><?= $a['preferred_date'] ?> <?= $a['preferred_time'] ?></td>
                            <td>
                                <?php
                                $colors = [
                                    'Pending' => 'warning', 'Approved' => 'success',
                                    'Rejected' => 'danger', 'Rescheduled' => 'info',
                                    'Completed' => 'primary', 'Cancelled' => 'secondary'
                                ];
                                $color = $colors[$a['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= $a['status'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="/recond_garage/pages/my_appointments.php" class="btn btn-outline-primary btn-sm">View All</a>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <h4>Quick Actions</h4>
            <div class="list-group">
                <a href="/recond_garage/pages/listings.php" class="list-group-item list-group-item-action">Browse Cars</a>
                <a href="/recond_garage/pages/loan_calculator.php" class="list-group-item list-group-item-action">Loan Calculator</a>
                <a href="/recond_garage/pages/my_appointments.php" class="list-group-item list-group-item-action">My Appointments</a>
                <a href="/recond_garage/profile.php" class="list-group-item list-group-item-action">Edit Profile</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>