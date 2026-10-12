<link rel="stylesheet" href="/recond_garage/assets/css/style.css">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/recond_garage/index.php">Recond Garage</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <div class="navbar-nav me-auto">
                <a class="nav-link" href="/recond_garage/pages/listings.php">Cars</a>
                <a class="nav-link" href="/recond_garage/pages/loan_calculator.php">Loan Calculator</a>
                <a class="nav-link" href="/recond_garage/pages/contact.php">Contact</a>
                <a class="nav-link" href="/recond_garage/pages/help.php">Help</a>

                <?php if (isLoggedIn() && getUserRole() === 'customer'): ?>
                    <a class="nav-link" href="/recond_garage/customer/dashboard.php">My Dashboard</a>
                    <a class="nav-link" href="/recond_garage/pages/my_appointments.php">My Appointments</a>
                <?php endif; ?>

                <?php if (isLoggedIn() && getUserRole() === 'seller'): ?>
                    <a class="nav-link" href="/recond_garage/seller/dashboard.php">Seller Dashboard</a>
                <?php endif; ?>

                <?php if (isLoggedIn() && getUserRole() === 'admin'): ?>
                    <a class="nav-link" href="/recond_garage/admin/dashboard.php">Admin Dashboard</a>
                <?php endif; ?>
            </div>

            <div class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_SESSION['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/recond_garage/profile.php">My Profile</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/recond_garage/auth/logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="nav-link" href="/recond_garage/auth/login.php">Login</a>
                    <a class="nav-link" href="/recond_garage/auth/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>