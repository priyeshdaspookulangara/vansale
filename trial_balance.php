<?php
require_once 'includes/header.php';
requireRole(['Super Admin', 'Accountant']);
require_once 'includes/AccountingReportManager.php';

$manager = new AccountingReportManager($pdo);
$asOfDate = $_GET['as_of_date'] ?? date('Y-m-d');
$reportData = $manager->getTrialBalance($asOfDate);

$grandTotalDebit = 0;
$grandTotalCredit = 0;
?>

<div class="page-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">Trial Balance</h1>
            <p class="text-muted">Consolidated summary of all ledger balances.</p>
        </div>
        <form class="d-flex gap-2" method="GET">
            <input type="date" name="as_of_date" value="<?= htmlspecialchars($asOfDate) ?>" class="form-control form-control-sm">
            <button type="submit" class="btn btn-primary btn-sm">Refresh</button>
        </form>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Type</th>
                            <th class="text-end">Debit (₹)</th>
                            <th class="text-end">Credit (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row):
                            $grandTotalDebit += (float)$row['total_debit'];
                            $grandTotalCredit += (float)$row['total_credit'];
                        ?>
                            <tr>
                                <td><code class="text-dark"><?= htmlspecialchars($row['account_code']) ?></code></td>
                                <td><?= htmlspecialchars($row['account_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['account_type']) ?></span></td>
                                <td class="text-end"><?= number_format($row['total_debit'], 2) ?></td>
                                <td class="text-end"><?= number_format($row['total_credit'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">TOTAL</td>
                            <td class="text-end"><?= number_format($grandTotalDebit, 2) ?></td>
                            <td class="text-end"><?= number_format($grandTotalCredit, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php if (abs($grandTotalDebit - $grandTotalCredit) > 0.01): ?>
        <div class="alert alert-warning mt-3 border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i> Trial Balance is out of sync.</div>
    <?php else: ?>
        <div class="alert alert-success mt-3 border-0 shadow-sm text-center py-2"><i class="bi bi-check-circle-fill me-2"></i> Accounts are perfectly in balance.</div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
