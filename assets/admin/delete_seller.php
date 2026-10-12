<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('admin');

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: sellers.php");
    exit;
}

// Get seller
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'seller'");
$stmt->execute([$id]);
$seller = $stmt->fetch();

if (!$seller) {
    die("Seller not found.");
}

// Delete all car images first
$stmt = $pdo->prepare("SELECT image FROM cars WHERE seller_id = ?");
$stmt->execute([$id]);
$images = $stmt->fetchAll();

foreach ($images as $img) {
    if ($img['image'] && file_exists(__DIR__ . '/../assets/uploads/cars/' . $img['image'])) {
        unlink(__DIR__ . '/../assets/uploads/cars/' . $img['image']);
    }
}

// Delete seller (cars & appointments auto-delete via CASCADE)
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'seller'");
$stmt->execute([$id]);

header("Location: sellers.php?removed=1");
exit;