<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('seller');

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: appointments.php");
    exit;
}

// Verify appointment belongs to this seller OR assigned to this seller
$stmt = $pdo->prepare("SELECT * FROM appointments 
                       WHERE id = ? 
                       AND (seller_id = ? OR assigned_seller_id = ?)");
$stmt->execute([$id, $_SESSION['user_id'], $_SESSION['user_id']]);
$appointment = $stmt->fetch();

if (!$appointment) {
    die("Appointment not found or you don't have permission.");
}

// Only allow delete kalau status dah settle
$deletableStatuses = ['Completed', 'Rejected', 'Cancelled'];
if (!in_array($appointment['status'], $deletableStatuses)) {
    die("Cannot delete appointment with status: " . $appointment['status'] . ". Only Completed, Rejected, or Cancelled appointments can be deleted.");
}

$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->execute([$id]);

header("Location: appointments.php?deleted=1");
exit;