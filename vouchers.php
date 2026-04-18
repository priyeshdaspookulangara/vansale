<?php require_once "auth_check.php"; ?>
requireRole(["Super Admin", "Accountant"]);
<?php
/**
 * vouchers.php
 *
 * Manual Voucher Entry UI.
 */

require 'includes/config.php';
require 'includes/ManualVoucherManager.php';

$manager = new ManualVoucherManager($pdo);
$heads = $pdo->query("SELECT id, name, code FROM account_heads ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $manager->createVoucher($_POST);
        header("Location: vouchers.php?id=$id&msg=success");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Vouchers - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Van Sales ERP</a>
    </div>
</nav>

<div class="container">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">New Manual Voucher</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Voucher Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Receipt">Receipt Voucher</option>
                            <option value="Payment">Payment Voucher</option>
                            <option value="Journal">Journal Voucher</option>
                            <option value="Contra">Contra Voucher</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Reference No</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Narrative">
                    </div>
                </div>

                <h6>Entry Lines</h6>
                <div id="voucherItemsContainer">
                    <!-- Row 1 (Debit) -->
                    <div class="row g-2 align-items-end mb-2 item-row">
                        <div class="col-md-4">
                            <label class="form-label small">Account</label>
                            <select name="items[0][account_id]" class="form-select" required>
                                <option value="">Select Account...</option>
                                <?php foreach ($heads as $h): ?>
                                    <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?> (<?= $h['code'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Debit</label>
                            <input type="number" name="items[0][debit]" step="0.01" class="form-control debit-input" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Credit</label>
                            <input type="number" name="items[0][credit]" step="0.01" class="form-control credit-input" value="0.00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Memo</label>
                            <input type="text" name="items[0][memo]" class="form-control">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
                        </div>
                    </div>
                    <!-- Row 2 (Credit) -->
                    <div class="row g-2 align-items-end mb-2 item-row">
                        <div class="col-md-4">
                            <select name="items[1][account_id]" class="form-select" required>
                                <option value="">Select Account...</option>
                                <?php foreach ($heads as $h): ?>
                                    <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?> (<?= $h['code'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[1][debit]" step="0.01" class="form-control debit-input" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="items[1][credit]" step="0.01" class="form-control credit-input" value="0.00">
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="items[1][memo]" class="form-control">
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addItemBtn">+ Add Entry Row</button>
                    <div>
                        <span class="me-3">Debits: <strong id="totalDebits">0.00</strong></span>
                        <span class="me-3">Credits: <strong id="totalCredits">0.00</strong></span>
                        <button type="submit" class="btn btn-success" id="submitBtn">Save Voucher</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let itemCount = 2;
    const accountOptions = `<?= str_replace(["\r", "\n"], '', addslashes(
        implode('', array_map(fn($h) => "<option value='{$h['id']}'>".htmlspecialchars($h['name'])." ({$h['code']})</option>", $heads))
    )) ?>`;

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('voucherItemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 align-items-end mb-2 item-row';
        newRow.innerHTML = `
            <div class="col-md-4">
                <select name="items[\${itemCount}][account_id]" class="form-select" required>
                    <option value="">Select Account...</option>
                    \${accountOptions}
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[\${itemCount}][debit]" step="0.01" class="form-control debit-input" value="0.00">
            </div>
            <div class="col-md-2">
                <input type="number" name="items[\${itemCount}][credit]" step="0.01" class="form-control credit-input" value="0.00">
            </div>
            <div class="col-md-3">
                <input type="text" name="items[\${itemCount}][memo]" class="form-control">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-item">X</button>
            </div>
        `;
        container.appendChild(newRow);
        itemCount++;
        calculateTotals();
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
            calculateTotals();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('debit-input') || e.target.classList.contains('credit-input')) {
            calculateTotals();
        }
    });

    function calculateTotals() {
        let debits = 0;
        let credits = 0;
        document.querySelectorAll('.debit-input').forEach(i => debits += parseFloat(i.value || 0));
        document.querySelectorAll('.credit-input').forEach(i => credits += parseFloat(i.value || 0));

        document.getElementById('totalDebits').innerText = debits.toFixed(2);
        document.getElementById('totalCredits').innerText = credits.toFixed(2);

        const submitBtn = document.getElementById('submitBtn');
        if (Math.abs(debits - credits) > 0.01 || debits === 0) {
            submitBtn.classList.replace('btn-success', 'btn-outline-danger');
            submitBtn.disabled = true;
        } else {
            submitBtn.classList.replace('btn-outline-danger', 'btn-success');
            submitBtn.disabled = false;
        }
    }

    calculateTotals();
</script>

</body>
</html>
