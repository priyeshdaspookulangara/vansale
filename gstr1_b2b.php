<?php require_once "auth_check.php"; ?>
requireRole(["Super Admin", "Accountant"]);
<?php
/**
 * gstr1_b2b.php
 *
 * GSTR-1 B2B Sales Report Interface.
 */

require 'includes/config.php';
require 'includes/GSTReportManager.php';

$manager = new GSTReportManager($pdo);
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-t');     // Last day of current month
$reportData = $manager->getB2BSalesReport($startDate, $endDate);

$totalTaxable = 0;
$totalCGST = 0;
$totalSGST = 0;
$totalIGST = 0;
$totalInvoiceValue = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GSTR-1 B2B Sales Report - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .report-header { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; }
        .table-report th { font-size: 0.85rem; background-color: #f1f3f5; }
        .total-row { border-top: 2px solid #343a40; font-weight: bold; background-color: #f8f9fa; }
    </style>
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Van Sales ERP</a>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 report-header p-3 rounded">
        <div>
            <h2 class="mb-0">GSTR-1 B2B Sales Report</h2>
            <small class="text-muted">Invoices for customers with GSTIN</small>
        </div>
        <form class="row g-2" method="GET">
            <div class="col-auto">
                <label class="small text-muted">From:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <label class="small text-muted">To:</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="form-control form-control-sm">
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary btn-sm">Generate</button>
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 table-report">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Customer GSTIN</th>
                            <th>State</th>
                            <th class="text-end">Taxable Value (₹)</th>
                            <th class="text-end">CGST (₹)</th>
                            <th class="text-end">SGST (₹)</th>
                            <th class="text-end">IGST (₹)</th>
                            <th class="text-end">Total Invoice Value (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reportData)): ?>
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted italic">No B2B sales records found for this period.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportData as $index => $row):
                                $totalTaxable += (float)$row['total_taxable_value'];
                                $totalCGST += (float)$row['total_cgst'];
                                $totalSGST += (float)$row['total_sgst'];
                                $totalIGST += (float)$row['total_igst'];
                                $totalInvoiceValue += (float)$row['total_amount'];
                            ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($row['invoice_no']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['invoice_date']) ?></td>
                                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                    <td><code class="text-primary"><?= htmlspecialchars($row['customer_gstin']) ?></code></td>
                                    <td class="text-center"><?= htmlspecialchars($row['customer_state_code']) ?></td>
                                    <td class="text-end"><?= number_format($row['total_taxable_value'], 2) ?></td>
                                    <td class="text-end text-success"><?= number_format($row['total_cgst'], 2) ?></td>
                                    <td class="text-end text-success"><?= number_format($row['total_sgst'], 2) ?></td>
                                    <td class="text-end text-success"><?= number_format($row['total_igst'], 2) ?></td>
                                    <td class="text-end fw-bold"><?= number_format($row['total_amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="6" class="text-end">TOTALS</td>
                            <td class="text-end"><?= number_format($totalTaxable, 2) ?></td>
                            <td class="text-end"><?= number_format($totalCGST, 2) ?></td>
                            <td class="text-end"><?= number_format($totalSGST, 2) ?></td>
                            <td class="text-end"><?= number_format($totalIGST, 2) ?></td>
                            <td class="text-end"><?= number_format($totalInvoiceValue, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 row">
        <div class="col-md-4">
            <div class="card border-primary mb-3">
                <div class="card-body py-2">
                    <small class="text-muted d-block">Total Taxable Value</small>
                    <h4 class="text-primary mb-0">₹<?= number_format($totalTaxable, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success mb-3">
                <div class="card-body py-2">
                    <small class="text-muted d-block">Total GST Liability</small>
                    <h4 class="text-success mb-0">₹<?= number_format($totalCGST + $totalSGST + $totalIGST, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-dark mb-3">
                <div class="card-body py-2">
                    <small class="text-muted d-block">Total Invoice Value (B2B)</small>
                    <h4 class="text-dark mb-0">₹<?= number_format($totalInvoiceValue, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
