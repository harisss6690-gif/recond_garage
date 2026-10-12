<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

// Verify appointment belongs to this seller OR assigned to this seller
$stmt = $pdo->prepare("SELECT * FROM appointments 
                       WHERE id = ? 
                       AND (seller_id = ? OR assigned_seller_id = ?)");
$stmt->execute([$id, $_SESSION['user_id'], $_SESSION['user_id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    die("Appointment not found or you don't have permission.");
}

$statusMap = [
    'approve' => 'Approved',
    'reject' => 'Rejected',
    'complete' => 'Completed',
    'cancel' => 'Cancelled',
];

if (!isset($statusMap[$action])) {
    die("Invalid action.");
}

$stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
$stmt->execute([$statusMap[$action], $id]);

header("Location: appointments.php?updated=1");
exit;