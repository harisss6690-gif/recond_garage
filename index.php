<?php
require_once 'includes/auth.php';
require_once 'config/db.php';

// Stats
$totalCars = $pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'available'")->fetchColumn();
$totalSellers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller'")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();

// Featured cars (6 terbaru)
$stmt = $pdo->query("SELECT * FROM cars WHERE status = 'available' ORDER BY created_at DESC LIMIT 6");
$featured = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recond Garage - Find Your Dream Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="bg-dark text-white py-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Find Your Dream Car</h1>
        <p class="lead">Browse reconditioned and used cars at Recond Garage</p>
        <a href="/recond_garage/pages/listings.php" class="btn btn-primary btn-lg mt-2">Browse Cars</a>
        <a href="/recond_garage/pages/loan_calculator.php" class="btn btn-outline-light btn-lg mt-2">Calculate Loan</a>
    </div>
</div>

<!-- Stats -->
<div class="container mt-5">
    <div class="row text-center">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-primary"><?= $totalCars ?></h2>
                    <p class="text-muted mb-0">Cars Available</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-warning"><?= $totalSellers ?></h2>
                    <p class="text-muted mb-0">Trusted Sellers</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="text-success"><?= $totalCustomers ?></h2>
                    <p class="text-muted mb-0">Happy Customers</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Cars -->
<div class="container mt-5">
    <h2 class="mb-4">Featured Cars</h2>
    <?php if (count($featured) === 0): ?>
        <div class="alert alert-info">No cars available yet. Check back soon!</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($featured as $car): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if ($car['image']): ?>
                        <img src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($car['image']) ?>" 
                             class="card-img-top" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                             style="height: 200px;">No Image</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h5>
                        <p class="text-muted small mb-2">
                            <?= $car['year'] ?> | <?= number_format($car['mileage']) ?> km | <?= $car['transmission'] ?>
                        </p>
                        <p class="text-success fw-bold">RM <?= number_format($car['price'], 2) ?></p>
                        <a href="/recond_garage/pages/car_details.php?id=<?= $car['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="/recond_garage/pages/listings.php" class="btn btn-outline-primary">View All Cars</a>
        </div>
    <?php endif; ?>
</div>

<!-- Why Choose Us -->
<div class="bg-light py-5 mt-5">
    <div class="container">
        <h2 class="text-center mb-4">Why Choose Recond Garage?</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-3">
                <h5>Trusted Sellers</h5>
                <p class="text-muted">All sellers are registered and verified by our team.</p>
            </div>
            <div class="col-md-4 text-center mb-3">
                <h5>Easy Booking</h5>
                <p class="text-muted">Book viewing or test drive appointments online.</p>
            </div>
            <div class="col-md-4 text-center mb-3">
                <h5>Loan Calculator</h5>
                <p class="text-muted">Estimate your monthly installment instantly.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>