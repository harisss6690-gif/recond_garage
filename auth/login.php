<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

if (isLoggedIn()) {
    header("Location: /recond_garage/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = 'Your account is inactive. Contact admin.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: /recond_garage/admin/dashboard.php");
                } elseif ($user['role'] === 'seller') {
                    header("Location: /recond_garage/seller/dashboard.php");
                } else {
                    header("Location: /recond_garage/customer/dashboard.php");
                }
                exit;
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5 mb-5" style="max-width: 500px; padding: 0 20px;">
    <h2>Login</h2>

    <?php if (isset($_GET['registered'])): ?>
        <div class="alert alert-success">Registration successful. Please login.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Login</button>
        <a href="register.php" class="btn btn-link">No account? Register</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>