<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('admin');

// Stats
$totalCars = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
$totalAvailable = $pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'available'")->fetchColumn();
$totalSellers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller'")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
$pendingAppointments = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'Pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>.</p>

    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Cars</h5>
                    <h2><?= $totalCars ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Available</h5>
                    <h2><?= $totalAvailable ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Sellers</h5>
                    <h2><?= $totalSellers ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Customers</h5>
                    <h2><?= $totalCustomers ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Appointments</h5>
                    <p>Total: <strong><?= $totalAppointments ?></strong></p>
                    <p>Pending: <strong class="text-danger"><?= $pendingAppointments ?></strong></p>
                    <a href="appointments.php" class="btn btn-sm btn-primary">Manage Appointments</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Quick Links</h5>
                    <a href="listings.php" class="btn btn-sm btn-outline-primary">All Listings</a>
                    <a href="sellers.php" class="btn btn-sm btn-outline-warning">Manage Sellers</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>