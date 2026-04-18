<?php
require_once 'includes/header.php';
requireRole(['Super Admin', 'Accountant']);
require_once 'includes/ManualVoucherManager.php';

$manager = new ManualVoucherManager($pdo);
$heads = $pdo->query("SELECT id, name, code FROM account_heads ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $manager->createVoucher($_POST);
        $successMsg = "Voucher created successfully. ID: $id";
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="page-header">
    <div class="container-fluid">
        <h1 class="h3 mb-0">Manual Accounting Vouchers</h1>
        <p class="text-muted">Record double-entry transactions for expenses, receipts, and journals.</p>
    </div>
</div>

<div class="container-fluid px-4">
    <?php if (isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if (isset($successMsg)): ?><div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div><?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Voucher Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Receipt">Receipt Voucher</option>
                            <option value="Payment">Payment Voucher</option>
                            <option value="Journal">Journal Voucher</option>
                            <option value="Contra">Contra Voucher</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Reference No</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Narrative">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th style="width: 150px;">Debit</th>
                                <th style="width: 150px;">Credit</th>
                                <th>Memo</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="voucherItemsContainer">
                            <tr class="item-row">
                                <td>
                                    <select name="items[0][account_id]" class="form-select form-select-sm" required>
                                        <option value="">Select Account...</option>
                                        <?php foreach ($heads as $h): ?>
                                            <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?> (<?= $h['code'] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="items[0][debit]" step="0.01" class="form-control form-control-sm debit-input" value="0.00"></td>
                                <td><input type="number" name="items[0][credit]" step="0.01" class="form-control form-control-sm credit-input" value="0.00"></td>
                                <td><input type="text" name="items[0][memo]" class="form-control form-control-sm"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">+ Add Row</button>
                    <div class="text-end">
                        <div class="mb-2">Debits: <strong id="totalDebits">0.00</strong> | Credits: <strong id="totalCredits">0.00</strong></div>
                        <button type="submit" class="btn btn-dark" id="submitBtn">Post Voucher</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let itemCount = 1;
    const accountOptions = `<?= str_replace(["\r", "\n"], '', addslashes(implode('', array_map(fn($h) => "<option value='{$h['id']}'>".htmlspecialchars($h['name'])." ({$h['code']})</option>", $heads)))) ?>`;

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('voucherItemsContainer');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td><select name="items[\${itemCount}][account_id]" class="form-select form-select-sm" required><option value="">Select Account...</option>\${accountOptions}</select></td>
            <td><input type="number" name="items[\${itemCount}][debit]" step="0.01" class="form-control form-control-sm debit-input" value="0.00"></td>
            <td><input type="number" name="items[\${itemCount}][credit]" step="0.01" class="form-control form-control-sm credit-input" value="0.00"></td>
            <td><input type="text" name="items[\${itemCount}][memo]" class="form-control form-control-sm"></td>
            <td><button type="button" class="btn btn-sm text-danger remove-item"><i class="material-icons fs-6">delete</i></button></td>
        `;
        container.appendChild(newRow);
        itemCount++;
        calculateTotals();
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
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
        let debits = 0, credits = 0;
        document.querySelectorAll('.debit-input').forEach(i => debits += parseFloat(i.value || 0));
        document.querySelectorAll('.credit-input').forEach(i => credits += parseFloat(i.value || 0));
        document.getElementById('totalDebits').innerText = debits.toFixed(2);
        document.getElementById('totalCredits').innerText = credits.toFixed(2);
        const submitBtn = document.getElementById('submitBtn');
        if (Math.abs(debits - credits) > 0.01 || debits === 0) {
            submitBtn.disabled = true;
            submitBtn.classList.replace('btn-dark', 'btn-outline-danger');
        } else {
            submitBtn.disabled = false;
            submitBtn.classList.replace('btn-outline-danger', 'btn-dark');
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>
