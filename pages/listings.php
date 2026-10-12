<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Filter
$where = ["c.status = 'available'"];
$params = [];

if (!empty($_GET['brand'])) {
    $where[] = "brand LIKE ?";
    $params[] = '%' . $_GET['brand'] . '%';
}
if (!empty($_GET['transmission'])) {
    $where[] = "transmission = ?";
    $params[] = $_GET['transmission'];
}
if (!empty($_GET['fuel_type'])) {
    $where[] = "fuel_type = ?";
    $params[] = $_GET['fuel_type'];
}
if (!empty($_GET['min_price'])) {
    $where[] = "price >= ?";
    $params[] = (float)$_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $where[] = "price <= ?";
    $params[] = (float)$_GET['max_price'];
}

$sql = "SELECT c.*, u.name AS seller_name FROM cars c 
        JOIN users u ON c.seller_id = u.id 
        WHERE " . implode(' AND ', $where) . " 
        ORDER BY c.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Car Listings - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Car Listings</h1>

    <!-- Filter -->
    <form method="GET" class="row g-3 mb-4 p-3 bg-light rounded">
        <div class="col-md-3">
            <label>Brand</label>
            <input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($_GET['brand'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label>Transmission</label>
            <select name="transmission" class="form-select">
                <option value="">All</option>
                <option value="Auto" <?= ($_GET['transmission'] ?? '') === 'Auto' ? 'selected' : '' ?>>Auto</option>
                <option value="Manual" <?= ($_GET['transmission'] ?? '') === 'Manual' ? 'selected' : '' ?>>Manual</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Fuel</label>
            <select name="fuel_type" class="form-select">
                <option value="">All</option>
                <option value="Petrol" <?= ($_GET['fuel_type'] ?? '') === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                <option value="Diesel" <?= ($_GET['fuel_type'] ?? '') === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                <option value="Hybrid" <?= ($_GET['fuel_type'] ?? '') === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                <option value="Electric" <?= ($_GET['fuel_type'] ?? '') === 'Electric' ? 'selected' : '' ?>>Electric</option>
            </select>
        </div>
        <div class="col-md-2">
            <label>Min Price</label>
            <input type="number" name="min_price" class="form-control" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <label>Max Price</label>
            <input type="number" name="max_price" class="form-control" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- Listings -->
    <?php if (count($cars) === 0): ?>
        <div class="alert alert-info">No cars found matching your criteria.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($cars as $car): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <?php if ($car['image']): ?>
                        <img src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($car['image']) ?>" 
                             class="card-img-top" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                             style="height: 200px;">No Image</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h5>
                        <p class="card-text mb-1"><strong>Year:</strong> <?= $car['year'] ?></p>
                        <p class="card-text mb-1"><strong>Mileage:</strong> <?= number_format($car['mileage']) ?> km</p>
                        <p class="card-text mb-1"><strong>Transmission:</strong> <?= $car['transmission'] ?></p>
                        <p class="card-text mb-2"><strong>Price:</strong> 
                            <span class="text-success fw-bold">RM <?= number_format($car['price'], 2) ?></span>
                        </p>
                        <a href="car_details.php?id=<?= $car['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
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