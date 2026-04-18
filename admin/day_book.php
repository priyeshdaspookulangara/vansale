<?php
/**
 * admin/day_book.php
 *
 * Day Book Report Interface.
 */

require_once '../auth_check.php';
requireRole(['Super Admin', 'Accountant']);
require_once '../includes/config.php';
require_once '../includes/AccountingReportManager.php';

$manager = new AccountingReportManager($pdo);
$date = $_GET['date'] ?? date('Y-m-d');
$reportData = $manager->getDayBook($date);

$currentEntryId = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day Book - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .entry-header { background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6; }
        .source-badge { font-size: 0.7rem; }
    </style>
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">Van Sales ERP - Day Book</a>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Day Book</h2>
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-auto">
                <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="form-control">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Go</button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Particulars / Account Name</th>
                            <th>Reference</th>
                            <th class="text-end" style="width: 150px;">Debit (₹)</th>
                            <th class="text-end" style="width: 150px;">Credit (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reportData)): ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted italic">No transactions recorded for this date.</td></tr>
                        <?php else: ?>
                            <?php foreach ($reportData as $row): ?>
                                <?php if ($currentEntryId !== $row['entry_id']): ?>
                                    <tr class="entry-header">
                                        <td colspan="2">
                                            <span class="badge bg-dark source-badge me-2"><?= strtoupper($row['source_type']) ?></span>
                                            <?= htmlspecialchars($row['description']) ?>
                                        </td>
                                        <td colspan="2" class="text-end text-muted small"><?= htmlspecialchars($row['reference_no']) ?></td>
                                    </tr>
                                    <?php $currentEntryId = $row['entry_id']; ?>
                                <?php endif; ?>
                                <tr>
                                    <td class="ps-4">
                                        <?= htmlspecialchars($row['account_name']) ?>
                                        <small class="text-muted">[<?= htmlspecialchars($row['account_code']) ?>]</small>
                                    </td>
                                    <td></td>
                                    <td class="text-end"><?= $row['debit'] > 0 ? number_format($row['debit'], 2) : '' ?></td>
                                    <td class="text-end"><?= $row['credit'] > 0 ? number_format($row['credit'], 2) : '' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
