<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireLogin();

if (getUserRole() !== 'customer') {
    header("Location: listings.php");
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, c.brand, c.model, c.year, s.name AS salesperson_name 
                       FROM appointments a 
                       JOIN cars c ON a.car_id = c.id 
                       LEFT JOIN users s ON a.assigned_seller_id = s.id 
                       WHERE a.customer_id = ? 
                       ORDER BY a.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll();

// Google Maps link untuk Utama Motor World
$mapLink = 'https://www.google.com/maps/search/?api=1&query=Utama+Motor+World+Malaysia';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <h1>My Appointments</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Appointment booked successfully. Waiting for seller approval.</div>
    <?php endif; ?>

    <?php if (count($appointments) === 0): ?>
        <div class="alert alert-info">You have no appointments yet.</div>
    <?php else: ?>

        <!-- DESKTOP: Table -->
        <div class="appt-table mt-3">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Car</th>
                        <th>Type</th>
                        <th>Salesperson</th>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['brand'] . ' ' . $a['model'] . ' (' . $a['year'] . ')') ?></td>
                        <td><?= $a['type'] ?></td>
                        <td><?= htmlspecialchars($a['salesperson_name'] ?? '-') ?></td>
                        <td>
                            <?= $a['preferred_date'] ?><br>
                            <small><?= $a['preferred_time'] ?></small>
                        </td>
                        <td>
                            <a href="<?= $mapLink ?>" target="_blank" class="text-decoration-none">
                                Utama Motor World
                                <small class="text-primary">(View Map)</small>
                            </a>
                        </td>
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
                        <td><?= htmlspecialchars($a['remarks'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE: Card -->
        <div class="appt-cards mt-3">
            <?php foreach ($appointments as $a): ?>
                <?php
                $colors = [
                    'Pending' => 'warning', 'Approved' => 'success',
                    'Rejected' => 'danger', 'Rescheduled' => 'info',
                    'Completed' => 'primary', 'Cancelled' => 'secondary'
                ];
                $color = $colors[$a['status']] ?? 'secondary';
                ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0">
                                <?= htmlspecialchars($a['brand'] . ' ' . $a['model']) ?>
                            </h6>
                            <span class="badge bg-<?= $color ?>"><?= $a['status'] ?></span>
                        </div>
                        <p class="text-muted small mb-3">
                            <?= $a['year'] ?> | <?= $a['type'] ?>
                        </p>

                        <div class="row small">
                            <div class="col-6 mb-2">
                                <strong>Date:</strong><br>
                                <?= $a['preferred_date'] ?>
                            </div>
                            <div class="col-6 mb-2">
                                <strong>Time:</strong><br>
                                <?= $a['preferred_time'] ?>
                            </div>
                            <div class="col-6 mb-2">
                                <strong>Salesperson:</strong><br>
                                <?= htmlspecialchars($a['salesperson_name'] ?? '-') ?>
                            </div>
                            <div class="col-6 mb-2">
                                <strong>Location:</strong><br>
                                <a href="<?= $mapLink ?>" target="_blank" class="text-decoration-none">
                                    Utama Motor World<br>
                                    <small class="text-primary">View Map</small>
                                </a>
                            </div>
                            <?php if ($a['remarks']): ?>
                                <div class="col-12 mt-2">
                                    <strong>Remarks:</strong><br>
                                    <?= htmlspecialchars($a['remarks']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>