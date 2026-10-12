<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $year = (int)$_POST['year'];
    $price = (float)$_POST['price'];
    $mileage = (int)$_POST['mileage'];
    $transmission = $_POST['transmission'];
    $fuel_type = $_POST['fuel_type'];
    $condition_type = $_POST['condition_type'];
    $description = trim($_POST['description']);

    if (empty($brand) || empty($model) || empty($year) || empty($price)) {
        $error = 'Please fill in all required fields.';
    } else {
        $mainImage = null;
        $extraImages = [];
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        // Upload main image
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'Main image: only JPG, JPEG, PNG, WEBP allowed.';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'Main image must be less than 5MB.';
            } else {
                $mainImage = uniqid('car_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../assets/uploads/cars/' . $mainImage);
            }
        }

        // Upload extra images
        if (!empty($_FILES['images']['name'][0]) && !$error) {
            foreach ($_FILES['images']['name'] as $i => $name) {
                if (empty($name)) continue;
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($ext, $allowed) && $_FILES['images']['size'][$i] <= 5 * 1024 * 1024) {
                    $newName = uniqid('car_extra_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['images']['tmp_name'][$i], __DIR__ . '/../assets/uploads/cars/' . $newName)) {
                        $extraImages[] = $newName;
                    }
                }
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("INSERT INTO cars 
                (seller_id, brand, model, year, price, mileage, transmission, fuel_type, condition_type, description, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'], $brand, $model, $year, $price, $mileage,
                $transmission, $fuel_type, $condition_type, $description, $mainImage
            ]);
            $carId = $pdo->lastInsertId();

            // Insert extra images
            if (!empty($extraImages)) {
                $stmt = $pdo->prepare("INSERT INTO car_images (car_id, image) VALUES (?, ?)");
                foreach ($extraImages as $img) {
                    $stmt->execute([$carId, $img]);
                }
            }

            $success = 'Car listing added successfully.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Car - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
    <h2>Add New Car Listing</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Brand *</label>
                <input type="text" name="brand" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Model *</label>
                <input type="text" name="model" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Year *</label>
                <input type="number" name="year" class="form-control" min="1990" max="<?= date('Y') ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Price (RM) *</label>
                <input type="number" step="0.01" name="price" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Mileage (km) *</label>
                <input type="number" name="mileage" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Transmission</label>
                <select name="transmission" class="form-select">
                    <option value="Auto">Auto</option>
                    <option value="Manual">Manual</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Fuel Type</label>
                <select name="fuel_type" class="form-select">
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Hybrid">Hybrid</option>
                    <option value="Electric">Electric</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Condition</label>
                <select name="condition_type" class="form-select">
                    <option value="Used">Used</option>
                    <option value="New">New</option>
                    <option value="Recond">Recond</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="mb-3">
            <label>Main Image (thumbnail) *</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="text-muted">Gambar utama untuk listing card.</small>
        </div>

        <div class="mb-3">
            <label>Additional Images (boleh pilih banyak)</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            <small class="text-muted">Tahan Ctrl untuk pilih banyak gambar sekali.</small>
        </div>

        <button type="submit" class="btn btn-primary">Add Listing</button>
        <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>