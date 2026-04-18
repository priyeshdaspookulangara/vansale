<?php
require_once 'includes/header.php';
requireRole(['Super Admin', 'Accountant']);
require_once 'includes/GSTReportManager.php';

$manager = new GSTReportManager($pdo);
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');
$reportData = $manager->getB2BSalesReport($startDate, $endDate);

$totalTaxable = 0; $totalCGST = 0; $totalSGST = 0; $totalIGST = 0; $totalAmount = 0;
?>

<div class="page-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0">GSTR-1 B2B Report</h1>
            <p class="text-muted">Extract of B2B sales for GST filing.</p>
        </div>
        <form class="d-flex gap-2" method="GET">
            <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="form-control form-control-sm">
            <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="form-control form-control-sm">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        </form>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Inv No</th>
                            <th>Date</th>
                            <th>Customer GSTIN</th>
                            <th class="text-end">Taxable</th>
                            <th class="text-end">CGST</th>
                            <th class="text-end">SGST</th>
                            <th class="text-end">IGST</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row):
                            $totalTaxable += $row['total_taxable_value'];
                            $totalCGST += $row['total_cgst'];
                            $totalSGST += $row['total_sgst'];
                            $totalIGST += $row['total_igst'];
                            $totalAmount += $row['total_amount'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                                <td><?= htmlspecialchars($row['invoice_date']) ?></td>
                                <td><code><?= htmlspecialchars($row['customer_gstin']) ?></code></td>
                                <td class="text-end"><?= number_format($row['total_taxable_value'], 2) ?></td>
                                <td class="text-end"><?= number_format($row['total_cgst'], 2) ?></td>
                                <td class="text-end"><?= number_format($row['total_sgst'], 2) ?></td>
                                <td class="text-end"><?= number_format($row['total_igst'], 2) ?></td>
                                <td class="text-end fw-bold"><?= number_format($row['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="3" class="text-end">TOTAL</td>
                            <td class="text-end"><?= number_format($totalTaxable, 2) ?></td>
                            <td class="text-end"><?= number_format($totalCGST, 2) ?></td>
                            <td class="text-end"><?= number_format($totalSGST, 2) ?></td>
                            <td class="text-end"><?= number_format($totalIGST, 2) ?></td>
                            <td class="text-end"><?= number_format($totalAmount, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
