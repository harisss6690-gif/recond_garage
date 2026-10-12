<?php
require_once '../includes/auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Calculator - Recond Garage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5 mb-5" style="max-width: 700px;">
    <h1>Car Loan Calculator</h1>
    <p class="text-muted">Estimate your monthly installment using Flat Rate (standard for car loans in Malaysia).</p>

    <form id="loanForm" class="row g-3 mt-3">
        <div class="col-md-6">
            <label>Car Price (RM)</label>
            <input type="number" id="carPrice" class="form-control" value="80000" min="0" required>
        </div>
        <div class="col-md-6">
            <label>Down Payment (RM)</label>
            <input type="number" id="downPayment" class="form-control" value="8000" min="0" required>
        </div>
        <div class="col-md-6">
            <label>Interest Rate (% per annum)</label>
            <input type="number" id="interestRate" class="form-control" value="3.5" step="0.01" min="0" required>
        </div>
        <div class="col-md-6">
            <label>Loan Tenure (Years)</label>
            <input type="number" id="loanTenure" class="form-control" value="7" min="1" max="15" required>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Calculate</button>
            <button type="reset" class="btn btn-outline-secondary">Reset</button>
        </div>
    </form>

    <div id="result" class="mt-4" style="display: none;">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Calculation Result</h5>
            </div>
            <div class="card-body">
                <table class="table mb-0">
                    <tr>
                        <th>Loan Amount</th>
                        <td id="rLoanAmount">-</td>
                    </tr>
                    <tr>
                        <th>Total Interest</th>
                        <td id="rTotalInterest">-</td>
                    </tr>
                    <tr>
                        <th>Total Loan</th>
                        <td id="rTotalLoan">-</td>
                    </tr>
                    <tr class="table-success">
                        <th>Monthly Installment</th>
                        <td><strong id="rMonthly">-</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div id="errorBox" class="alert alert-danger mt-3" style="display: none;"></div>
</div>

<script>
document.getElementById('loanForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const carPrice    = parseFloat(document.getElementById('carPrice').value) || 0;
    const downPayment = parseFloat(document.getElementById('downPayment').value) || 0;
    const rate        = parseFloat(document.getElementById('interestRate').value) || 0;
    const years       = parseFloat(document.getElementById('loanTenure').value) || 0;

    const errorBox = document.getElementById('errorBox');
    const result   = document.getElementById('result');

    if (downPayment < 0) {
        showError('Down payment cannot be negative.');
        return;
    }
    if (downPayment >= carPrice) {
        showError('Down payment must be less than the car price.');
        return;
    }
    if (rate < 0 || years <= 0) {
        showError('Please enter valid interest rate and loan tenure.');
        return;
    }

    const principal = carPrice - downPayment;
    const months    = years * 12;

    // Flat Rate Formula
    const totalInterest = principal * (rate / 100) * years;
    const totalLoan     = principal + totalInterest;
    const monthly       = totalLoan / months;

    document.getElementById('rLoanAmount').textContent    = 'RM ' + formatNumber(principal);
    document.getElementById('rTotalInterest').textContent = 'RM ' + formatNumber(totalInterest);
    document.getElementById('rTotalLoan').textContent     = 'RM ' + formatNumber(totalLoan);
    document.getElementById('rMonthly').textContent       = 'RM ' + formatNumber(monthly);

    errorBox.style.display = 'none';
    result.style.display   = 'block';
});

function formatNumber(num) {
    return num.toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function showError(msg) {
    const errorBox = document.getElementById('errorBox');
    errorBox.textContent = msg;
    errorBox.style.display = 'block';
    document.getElementById('result').style.display = 'none';
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>