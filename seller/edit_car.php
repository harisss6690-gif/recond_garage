<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND seller_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found or you don't have permission.");
}

$error = '';
$success = '';

// Handle delete extra image
if (isset($_GET['delete_image'])) {
    $imgId = (int)$_GET['delete_image'];
    $stmt = $pdo->prepare("SELECT * FROM car_images WHERE id = ? AND car_id = ?");
    $stmt->execute([$imgId, $id]);
    $img = $stmt->fetch();
    if ($img) {
        if (file_exists(__DIR__ . '/../assets/uploads/cars/' . $img['image'])) {
            unlink(__DIR__ . '/../assets/uploads/cars/' . $img['image']);
        }
        $stmt = $pdo->prepare("DELETE FROM car_images WHERE id = ?");
        $stmt->execute([$imgId]);
        header("Location: edit_car.php?id=$id&deleted=1");
        exit;
    }
}

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
    $status = $_POST['status'];

    if (empty($brand) || empty($model) || empty($year) || empty($price)) {
        $error = 'Please fill in all required fields.';
    } else {
        $imageName = $car['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        // New main image
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                $error = 'Main image: only JPG, JPEG, PNG, WEBP allowed.';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $error = 'Main image must be less than 5MB.';
            } else {
                if ($car['image'] && file_exists(__DIR__ . '/../assets/uploads/cars/' . $car['image'])) {
                    unlink(__DIR__ . '/../assets/uploads/cars/' . $car['image']);
                }
                $imageName = uniqid('car_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../assets/uploads/cars/' . $imageName);
            }
        }

        // New extra images
        $extraImages = [];
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
            $stmt = $pdo->prepare("UPDATE cars SET 
                brand = ?, model = ?, year = ?, price = ?, mileage = ?, 
                transmission = ?, fuel_type = ?, condition_type = ?, 
                description = ?, image = ?, status = ? 
                WHERE id = ? AND seller_id = ?");
            $stmt->execute([
                $brand, $model, $year, $price, $mileage,
                $transmission, $fuel_type, $condition_type,
                $description, $imageName, $status,
                $id, $_SESSION['user_id']
            ]);

            if (!empty($extraImages)) {
                $stmt = $pdo->prepare("INSERT INTO car_images (car_id, image) VALUES (?, ?)");
                foreach ($extraImages as $img) {
                    $stmt->execute([$id, $img]);
                }
            }

            $success = 'Listing updated successfully.';

            $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
            $stmt->execute([$id]);
            $car = $stmt->fetch();
        }
    }
}

// Fetch extra images
$stmt = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY id ASC");
$stmt->execute([$id]);
$extraImages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Car - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5" style="max-width: 700px;">
    <h2>Edit Car Listing</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">Image deleted.</div>
    <?php endif; ?>
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
                <input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($car['brand']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Model *</label>
                <input type="text" name="model" class="form-control" value="<?= htmlspecialchars($car['model']) ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Year *</label>
                <input type="number" name="year" class="form-control" min="1990" max="<?= date('Y') ?>" value="<?= $car['year'] ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Price (RM) *</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?= $car['price'] ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Mileage (km) *</label>
                <input type="number" name="mileage" class="form-control" value="<?= $car['mileage'] ?>" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Transmission</label>
                <select name="transmission" class="form-select">
                    <option value="Auto" <?= $car['transmission'] === 'Auto' ? 'selected' : '' ?>>Auto</option>
                    <option value="Manual" <?= $car['transmission'] === 'Manual' ? 'selected' : '' ?>>Manual</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Fuel Type</label>
                <select name="fuel_type" class="form-select">
                    <option value="Petrol" <?= $car['fuel_type'] === 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                    <option value="Diesel" <?= $car['fuel_type'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                    <option value="Hybrid" <?= $car['fuel_type'] === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                    <option value="Electric" <?= $car['fuel_type'] === 'Electric' ? 'selected' : '' ?>>Electric</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Condition</label>
                <select name="condition_type" class="form-select">
                    <option value="Used" <?= $car['condition_type'] === 'Used' ? 'selected' : '' ?>>Used</option>
                    <option value="New" <?= $car['condition_type'] === 'New' ? 'selected' : '' ?>>New</option>
                    <option value="Recond" <?= $car['condition_type'] === 'Recond' ? 'selected' : '' ?>>Recond</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($car['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label>Main Image (thumbnail)</label><br>
            <?php if ($car['image']): ?>
                <img src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($car['image']) ?>" width="150" class="mb-2">
            <?php else: ?>
                <p class="text-muted">No image</p>
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="text-muted">Leave empty to keep current main image.</small>
        </div>

        <!-- Extra Images -->
        <div class="mb-3">
            <label>Additional Images</label>
            <?php if (count($extraImages) > 0): ?>
                <div class="row mt-2">
                    <?php foreach ($extraImages as $img): ?>
                    <div class="col-md-3 mb-2">
                        <div class="position-relative">
                            <img src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($img['image']) ?>" 
                                 class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">
                            <a href="edit_car.php?id=<?= $id ?>&delete_image=<?= $img['id'] ?>" 
                               class="btn btn-sm btn-danger position-absolute top-0 end-0"
                               onclick="return confirm('Delete this image?')">×</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mt-2">No additional images.</p>
            <?php endif; ?>
            <input type="file" name="images[]" class="form-control mt-2" accept="image/*" multiple>
            <small class="text-muted">Add more images. Tahan Ctrl untuk pilih banyak.</small>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="available" <?= $car['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                <option value="sold" <?= $car['status'] === 'sold' ? 'selected' : '' ?>>Sold</option>
                <option value="removed" <?= $car['status'] === 'removed' ? 'selected' : '' ?>>Removed</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="dashboard.php" class="btn btn-link">Back to Dashboard</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>