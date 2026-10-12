<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('admin');

$stmt = $pdo->query("SELECT c.*, u.name AS seller_name, u.email AS seller_email 
                     FROM cars c 
                     JOIN users u ON c.seller_id = u.id 
                     ORDER BY c.created_at DESC");
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Listings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h1>All Car Listings (<?= count($cars) ?>)</h1>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Listing deleted successfully.</div>
    <?php endif; ?>

    <?php if (count($cars) === 0): ?>
        <div class="alert alert-info">No listings yet.</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Car</th>
                    <th>Price</th>
                    <th>Seller</th>
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
                    <td>
                        <strong><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></strong><br>
                        <small><?= $car['year'] ?> | <?= number_format($car['mileage']) ?> km</small>
                    </td>
                    <td>RM <?= number_format($car['price'], 2) ?></td>
                    <td>
                        <?= htmlspecialchars($car['seller_name']) ?><br>
                        <small><?= htmlspecialchars($car['seller_email']) ?></small>
                    </td>
                    <td>
                        <?php
                        $colors = ['available' => 'success', 'sold' => 'secondary', 'removed' => 'danger'];
                        $color = $colors[$car['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $color ?>"><?= $car['status'] ?></span>
                    </td>
                    <td>
                        <a href="/recond_garage/pages/car_details.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                        <a href="delete_car.php?id=<?= $car['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this listing?')">Remove</a>
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