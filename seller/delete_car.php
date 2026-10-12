<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

// Get car — pastikan milik seller ini
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND seller_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found or you don't have permission.");
}

// Delete image
if ($car['image'] && file_exists(__DIR__ . '/../assets/uploads/cars/' . $car['image'])) {
    unlink(__DIR__ . '/../assets/uploads/cars/' . $car['image']);
}

// Delete car (appointments akan auto-delete sebab ON DELETE CASCADE)
$stmt = $pdo->prepare("DELETE FROM cars WHERE id = ? AND seller_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

header("Location: dashboard.php?deleted=1");
exit;