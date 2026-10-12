<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

$stmt = $pdo->prepare("SELECT * FROM cars WHERE seller_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$cars = $stmt->fetchAll();

// Count pending appointments
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM appointments WHERE seller_id = ? AND status = 'Pending'");
$stmt->execute([$_SESSION['user_id']]);
$pendingCount = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Seller Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Car listing deleted successfully.</div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Seller Dashboard</h1>
        <div>
            <a href="appointments.php" class="btn btn-outline-primary">
                Appointments
                <?php if ($pendingCount > 0): ?>
                    <span class="badge bg-danger"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>
            <a href="add_car.php" class="btn btn-primary">+ Add New Car</a>
        </div>
    </div>

    <p>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>.</p>

    <h4 class="mt-4">My Listings (<?= count($cars) ?>)</h4>

    <?php if (count($cars) === 0): ?>
        <div class="alert alert-info">You have no listings yet. Click "Add New Car" to start.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Price (RM)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cars as $car): ?>
                <tr>
                    <td>
                        <?php if ($car['image']): ?>
                            <img src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($car['image']) ?>" width="80">
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($car['brand']) ?></td>
                    <td><?= htmlspecialchars($car['model']) ?></td>
                    <td><?= $car['year'] ?></td>
                    <td><?= number_format($car['price'], 2) ?></td>
                    <td><?= $car['status'] ?></td>
                    <td>
                        <a href="edit_car.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this listing?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>