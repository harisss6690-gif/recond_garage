<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('admin');

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: listings.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found.");
}

// Delete image
if ($car['image'] && file_exists(__DIR__ . '/../assets/uploads/cars/' . $car['image'])) {
    unlink(__DIR__ . '/../assets/uploads/cars/' . $car['image']);
}

// Delete car
$stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
$stmt->execute([$id]);

header("Location: listings.php?deleted=1");
exit;