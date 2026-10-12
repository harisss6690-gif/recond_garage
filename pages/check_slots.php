<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$seller_id = (int)($_GET['seller_id'] ?? 0);
$date = $_GET['date'] ?? '';

if (!$seller_id || !$date) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT preferred_time FROM appointments 
                       WHERE assigned_seller_id = ? 
                       AND preferred_date = ? 
                       AND status IN ('Pending', 'Approved')");
$stmt->execute([$seller_id, $date]);
$booked = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($booked);