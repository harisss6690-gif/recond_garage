<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireLogin();

if (getUserRole() !== 'customer') {
    header("Location: listings.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listings.php");
    exit;
}

$car_id = (int)$_POST['car_id'];
$type = $_POST['type'];
$preferred_date = $_POST['preferred_date'];
$preferred_time = $_POST['preferred_time'];
$remarks = trim($_POST['remarks'] ?? '');
$assigned_seller_id = (int)($_POST['assigned_seller_id'] ?? 0);

// Get seller_id dari car
$stmt = $pdo->prepare("SELECT seller_id FROM cars WHERE id = ?");
$stmt->execute([$car_id]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found.");
}

// Validate assigned seller
if (!$assigned_seller_id) {
    $assigned_seller_id = $car['seller_id'];
} else {
    $stmtS = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'seller' AND status = 'active'");
    $stmtS->execute([$assigned_seller_id]);
    if (!$stmtS->fetch()) {
        die("Invalid salesperson.");
    }
}

// ============================
// CONFLICT CHECK
// ============================
// Check kalau salesperson dah ada appointment pada slot sama (Pending / Approved)
$stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM appointments 
                            WHERE assigned_seller_id = ? 
                            AND preferred_date = ? 
                            AND preferred_time = ? 
                            AND status IN ('Pending', 'Approved')");
$stmtCheck->execute([$assigned_seller_id, $preferred_date, $preferred_time]);
$conflict = $stmtCheck->fetchColumn();

if ($conflict > 0) {
    // Slot dah ditempah — redirect balik dengan error
    header("Location: car_details.php?id=$car_id&error=slot_taken");
    exit;
}
// ============================

$stmt = $pdo->prepare("INSERT INTO appointments 
    (car_id, customer_id, seller_id, assigned_seller_id, type, preferred_date, preferred_time, remarks, location) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([
    $car_id,
    $_SESSION['user_id'],
    $car['seller_id'],
    $assigned_seller_id,
    $type,
    $preferred_date,
    $preferred_time,
    $remarks,
    'Utama Motor World'
]);

header("Location: my_appointments.php?success=1");
exit;