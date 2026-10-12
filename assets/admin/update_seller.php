<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('admin');

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['activate', 'deactivate'])) {
    header("Location: sellers.php");
    exit;
}

$newStatus = $action === 'activate' ? 'active' : 'inactive';

$stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'seller'");
$stmt->execute([$newStatus, $id]);

header("Location: sellers.php?updated=1");
exit;