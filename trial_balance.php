<?php require_once "auth_check.php"; ?>
requireRole(["Super Admin", "Accountant"]);
<?php
/**
 * trial_balance.php
 *
 * Trial Balance Report Interface.
 */

require 'includes/config.php';
require 'includes/AccountingReportManager.php';

$manager = new AccountingReportManager($pdo);
$asOfDate = $_GET['as_of_date'] ?? date('Y-m-d');
$reportData = $manager->getTrialBalance($asOfDate);

$grandTotalDebit = 0;
$grandTotalCredit = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Balance - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-report th { background-color: #f1f3f5; }
        .total-row { border-top: 2px solid #343a40; font-weight: bold; }
    </style>
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Van Sales ERP</a>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Trial Balance Report</h2>
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-auto">
                <label class="small text-muted">As of Date:</label>
                <input type="date" name="as_of_date" value="<?= htmlspecialchars($asOfDate) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary btn-sm">Refresh Report</button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-report">
                    <thead>
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Account Type</th>
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
                                <td><?= htmlspecialchars($row['account_code']) ?></td>
                                <td><?= htmlspecialchars($row['account_name']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['account_type']) ?></span></td>
                                <td class="text-end"><?= number_format($row['total_debit'], 2) ?></td>
                                <td class="text-end"><?= number_format($row['total_credit'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="text-end">GRAND TOTAL</td>
                            <td class="text-end"><?= number_format($grandTotalDebit, 2) ?></td>
                            <td class="text-end"><?= number_format($grandTotalCredit, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if (abs($grandTotalDebit - $grandTotalCredit) > 0.01): ?>
                <div class="alert alert-warning mt-3">
                    <strong>Warning:</strong> The Trial Balance is out of sync by <?= number_format(abs($grandTotalDebit - $grandTotalCredit), 2) ?>.
                </div>
            <?php else: ?>
                <div class="alert alert-success mt-3 p-2">
                    <small>Trial Balance is in balance.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
