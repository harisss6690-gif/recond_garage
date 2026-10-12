<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: appointments.php");
    exit;
}

$id = (int)$_POST['id'];
$location = trim($_POST['location']);

// Verify appointment belongs to this seller
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND seller_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    die("Appointment not found.");
}

$stmt = $pdo->prepare("UPDATE appointments SET location = ? WHERE id = ?");
$stmt->execute([$location, $id]);

header("Location: appointments.php?updated=1");
exit;