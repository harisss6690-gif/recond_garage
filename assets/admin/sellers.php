<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireRole('admin');

$stmt = $pdo->query("SELECT u.*, 
                     (SELECT COUNT(*) FROM cars WHERE seller_id = u.id) AS total_cars 
                     FROM users u 
                     WHERE u.role = 'seller' 
                     ORDER BY u.created_at DESC");
$sellers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Sellers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h1>Manage Sellers (<?= count($sellers) ?>)</h1>

    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Seller status updated.</div>
    <?php endif; ?>
    <?php if (isset($_GET['removed'])): ?>
        <div class="alert alert-success">Seller and all related listings have been removed.</div>
    <?php endif; ?>

    <?php if (count($sellers) === 0): ?>
        <div class="alert alert-info">No sellers registered yet.</div>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Total Listings</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sellers as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['phone'] ?? '-') ?></td>
                    <td><?= $s['total_cars'] ?></td>
                    <td>
                        <?php if ($s['status'] === 'active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($s['status'] === 'active'): ?>
                            <a href="update_seller.php?id=<?= $s['id'] ?>&action=deactivate" 
                               class="btn btn-sm btn-warning"
                               onclick="return confirm('Deactivate this seller?')">Deactivate</a>
                        <?php else: ?>
                            <a href="update_seller.php?id=<?= $s['id'] ?>&action=activate" 
                               class="btn btn-sm btn-success"
                               onclick="return confirm('Activate this seller?')">Activate</a>
                        <?php endif; ?>

                        <a href="delete_seller.php?id=<?= $s['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('WARNING: This will permanently delete this seller, all their car listings, and all related appointments. Are you sure?')">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-link mt-3">&larr; Back to Dashboard</a>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>