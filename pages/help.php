<?php
require_once '../includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <h1>User Manual</h1>
    <p class="text-muted">Panduan penggunaan Recond Garage</p>

    <!-- CUSTOMER -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">For Customer</h4>
        </div>
        <div class="card-body">
            <h5>1. Register / Login</h5>
            <p>Register akaun baru, then login untuk mula guna sistem.</p>

            <h5>2. Browse Cars</h5>
            <p>Klik <strong>Cars</strong> di navbar. Guna filter untuk cari kereta ikut brand, transmission, fuel, atau harga.</p>

            <h5>3. Book Appointment</h5>
            <p>Buka detail kereta → scroll ke <strong>Book Viewing / Test Drive</strong> → pilih salesperson, type, tarikh, dan slot masa → klik Book Appointment.</p>

            <h5>4. My Appointments</h5>
            <p>Klik <strong>My Appointments</strong> untuk tengok status janji temu.</p>

            <h5>5. Loan Calculator</h5>
            <p>Klik <strong>Loan Calculator</strong> → masukkan harga, down payment, interest, tenure → Calculate.</p>

            <h5>6. Profile</h5>
            <p>Klik nama anda → <strong>My Profile</strong> → edit nama, phone, atau password.</p>
        </div>
    </div>

    <!-- SELLER -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">For Seller</h4>
        </div>
        <div class="card-body">
            <h5>1. Login</h5>
            <p>Login guna akaun seller.</p>

            <h5>2. Add Car Listing</h5>
            <p>Seller Dashboard → <strong>+ Add New Car</strong> → isi maklumat kereta + upload gambar → submit.</p>

            <h5>3. Manage Listings</h5>
            <p>Edit atau delete listing dari Seller Dashboard.</p>

            <h5>4. Manage Appointments</h5>
            <p>Klik <strong>Appointments</strong> → approve / reject / complete appointment requests.</p>
        </div>
    </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>