<?php
require_once 'includes/auth.php';
require_once 'config/db.php';
requireLogin();

$error = '';
$success = '';

// Get current user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (empty($name)) {
        $error = 'Name cannot be empty.';
    } else {
        // Update basic info
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $_SESSION['user_id']]);
        $_SESSION['name'] = $name;

        // Change password (kalau diisi)
        if (!empty($newPass)) {
            if (!password_verify($currentPass, $user['password'])) {
                $error = 'Current password is incorrect.';
            } elseif ($newPass !== $confirmPass) {
                $error = 'New passwords do not match.';
            } elseif (strlen($newPass) < 6) {
                $error = 'Password must be at least 6 characters.';
            } else {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hash, $_SESSION['user_id']]);
                $success = 'Profile and password updated successfully.';
            }
        } else {
            $success = 'Profile updated successfully.';
        }

        // Refresh data
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Profile - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container mt-5" style="max-width: 600px;">
    <h1>My Profile</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p class="mb-0"><strong>Role:</strong> 
                <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
            </p>
        </div>
    </div>

    <h4>Edit Profile</h4>
    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>

        <hr class="my-4">
        <h5>Change Password</h5>
        <p class="text-muted small">Biarkan kosong kalau tak nak tukar password.</p>

        <div class="mb-3">
            <label>Current Password</label>
            <input type="password" name="current_password" class="form-control">
        </div>
        <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="/recond_garage/index.php" class="btn btn-link">Back</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>