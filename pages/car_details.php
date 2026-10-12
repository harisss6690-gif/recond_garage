<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: listings.php");
    exit;
}

$stmt = $pdo->prepare("SELECT c.*, u.name AS seller_name, u.email AS seller_email, u.phone AS seller_phone 
                       FROM cars c JOIN users u ON c.seller_id = u.id 
                       WHERE c.id = ?");
$stmt->execute([$id]);
$car = $stmt->fetch();

if (!$car) {
    die("Car not found.");
}

// Fetch extra images
$stmtImg = $pdo->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY id ASC");
$stmtImg->execute([$car['id']]);
$extraImgs = $stmtImg->fetchAll();

// Fetch semua seller
$stmtSellers = $pdo->query("SELECT id, name FROM users WHERE role = 'seller' AND status = 'active' ORDER BY name ASC");
$allSellers = $stmtSellers->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?> - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        option.booked-slot {
            font-weight: bold;
            color: #999;
            text-decoration: line-through;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <a href="listings.php" class="btn btn-link mb-3">&larr; Back to Listings</a>

    <div class="row">
        <div class="col-md-6">
            <?php if ($car['image']): ?>
                <img id="mainImage" src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($car['image']) ?>" 
                     class="img-fluid rounded mb-3" style="width: 100%; max-height: 400px; object-fit: cover;">
            <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded mb-3" 
                     style="height: 400px;">No Image</div>
            <?php endif; ?>

            <?php if (count($extraImgs) > 0): ?>
                <div class="row g-2">
                    <?php if ($car['image']): ?>
                        <div class="col-3">
                            <img src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($car['image']) ?>" 
                                 class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                                 onclick="document.getElementById('mainImage').src=this.src">
                        </div>
                    <?php endif; ?>
                    <?php foreach ($extraImgs as $img): ?>
                        <div class="col-3">
                            <img src="/recond_garage/assets/uploads/cars/<?= htmlspecialchars($img['image']) ?>" 
                                 class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;"
                                 onclick="document.getElementById('mainImage').src=this.src">
                        </div>
                    <?php endforeach; ?>
                </div>
                <small class="text-muted">Click thumbnails to change main image.</small>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h2><?= htmlspecialchars($car['brand'] . ' ' . $car['model']) ?></h2>
            <h3 class="text-success">RM <?= number_format($car['price'], 2) ?></h3>

            <table class="table mt-3">
                <tr><th>Year</th><td><?= $car['year'] ?></td></tr>
                <tr><th>Mileage</th><td><?= number_format($car['mileage']) ?> km</td></tr>
                <tr><th>Transmission</th><td><?= $car['transmission'] ?></td></tr>
                <tr><th>Fuel Type</th><td><?= $car['fuel_type'] ?></td></tr>
                <tr><th>Condition</th><td><?= $car['condition_type'] ?></td></tr>
                <tr><th>Status</th><td><?= $car['status'] ?></td></tr>
            </table>

            <?php if ($car['description']): ?>
                <h5>Description</h5>
                <p><?= nl2br(htmlspecialchars($car['description'])) ?></p>
            <?php endif; ?>

            <h5 class="mt-4">Seller Contact</h5>
            <p class="mb-0"><strong>Name:</strong> <?= htmlspecialchars($car['seller_name']) ?></p>
            <p class="mb-0"><strong>Email:</strong> <?= htmlspecialchars($car['seller_email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($car['seller_phone'] ?? '-') ?></p>

            <?php
            $waNumber = preg_replace('/[^0-9]/', '', $car['seller_phone'] ?? '');
            if ($waNumber && substr($waNumber, 0, 1) === '0') {
                $waNumber = '6' . $waNumber;
            }
            ?>
            <div class="mt-2">
                <?php if ($waNumber): ?>
                    <a href="https://wa.me/<?= $waNumber ?>?text=<?= urlencode('Hi, I am interested in your ' . $car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ') listed on Recond Garage.') ?>" 
                       target="_blank" class="btn btn-success btn-sm">
                        WhatsApp Seller
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <hr class="my-5">
    <h3>Book Viewing / Test Drive</h3>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'slot_taken'): ?>
        <div class="alert alert-danger">
            Slot ini sudah ditempah oleh customer lain. Sila pilih masa lain atau salesperson lain.
        </div>
    <?php endif; ?>

    <?php if (!isLoggedIn()): ?>
        <div class="alert alert-warning">
            Please <a href="/recond_garage/auth/login.php">login</a> to book an appointment.
        </div>
    <?php elseif (getUserRole() !== 'customer'): ?>
        <div class="alert alert-info">Only customers can book appointments.</div>
    <?php else: ?>
        <form action="book_appointment.php" method="POST" class="row g-3" style="max-width: 600px;">
            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">

            <div class="col-md-12">
                <label>Choose Salesperson</label>
                <select name="assigned_seller_id" id="sellerSelect" class="form-select" required>
                    <option value="">-- Select Salesperson --</option>
                    <?php foreach ($allSellers as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= $s['id'] == $car['seller_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?> <?= $s['id'] == $car['seller_id'] ? '(Owner)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label>Appointment Type</label>
                <select name="type" class="form-select" required>
                    <option value="Viewing">Viewing</option>
                    <option value="Test Drive">Test Drive</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Preferred Date</label>
                <input type="date" name="preferred_date" id="dateInput" class="form-control" min="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-6">
                <label>Preferred Time</label>
                <select name="preferred_time" id="timeSelect" class="form-select" required>
                    <option value="">-- Select Time Slot --</option>
                </select>
                <small class="text-muted">Slot bertanda <strong>BOOKED</strong> tak boleh dipilih.</small>
            </div>
            <div class="col-12">
                <label>Remarks (optional)</label>
                <textarea name="remarks" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Book Appointment</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
const sellerSelect = document.getElementById('sellerSelect');
const dateInput = document.getElementById('dateInput');
const timeSelect = document.getElementById('timeSelect');

const allSlots = [
    { value: '09:00:00', label: '9:00 AM' },
    { value: '10:00:00', label: '10:00 AM' },
    { value: '11:00:00', label: '11:00 AM' },
    { value: '14:00:00', label: '2:00 PM' },
    { value: '15:00:00', label: '3:00 PM' },
    { value: '16:00:00', label: '4:00 PM' },
    { value: '17:00:00', label: '5:00 PM' }
];

function renderSlots(bookedSlots) {
    timeSelect.innerHTML = '<option value="">-- Select Time Slot --</option>';
    allSlots.forEach(slot => {
        const opt = document.createElement('option');
        opt.value = slot.value;
        const isBooked = bookedSlots.includes(slot.value);
        if (isBooked) {
            opt.textContent = slot.label + ' — BOOKED';
            opt.disabled = true;
            opt.classList.add('booked-slot');
        } else {
            opt.textContent = slot.label;
        }
        timeSelect.appendChild(opt);
    });
}

function refreshSlots() {
    const sellerId = sellerSelect.value;
    const date = dateInput.value;

    if (!sellerId || !date) {
        renderSlots([]);
        return;
    }

    fetch(`check_slots.php?seller_id=${sellerId}&date=${date}`)
        .then(r => r.json())
        .then(booked => renderSlots(booked))
        .catch(() => renderSlots([]));
}

if (sellerSelect && dateInput && timeSelect) {
    sellerSelect.addEventListener('change', refreshSlots);
    dateInput.addEventListener('change', refreshSlots);
    refreshSlots();
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>