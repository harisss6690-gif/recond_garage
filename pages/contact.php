<?php
require_once '../includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h1>Contact Us</h1>
    <p class="text-muted">Visit our showroom or get in touch with us.</p>

    <div class="row mt-4">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h4>Utama Motor World</h4>
                    <hr>

                    <p>
                        <strong>Address</strong><br>
                        Utama Motor World<br>
                        Malaysia
                    </p>

                    <p>
                        <strong>Phone</strong><br>
                        <a href="tel:+60199132810">+60 19-913 2810</a>
                    </p>

                    <p>
                        <strong>Email</strong><br>
                        <a href="mailto:amrandir82@gmail.com">amrandir82@gmail.com</a>
                    </p>

                    <p class="mb-0">
                        <strong>Operating Hours</strong><br>
                        Monday - Saturday: 9:00 AM - 7:00 PM<br>
                        Sunday: 10:00 AM - 5:00 PM
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body p-0">
                    <iframe 
                        src="https://www.google.com/maps?q=Utama+Motor+World+Malaysia&output=embed"
                        width="100%" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>