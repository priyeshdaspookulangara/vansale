<?php require_once "auth_check.php"; ?>
requireRole(["Super Admin", "Accountant"]);
<?php
/**
 * stock_transfer.php
 *
 * Stock Transfer Management UI.
 */

require 'includes/config.php';
require 'includes/InventoryManager.php';

$manager = new InventoryManager($pdo);
$transferId = $_GET['voucher_id'] ?? null;
$voucher = $transferId ? $manager->getTransferVoucher($transferId) : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $transferData = [
            'warehouse_id' => (int)$_POST['warehouse_id'],
            'van_id'       => (int)$_POST['van_id'],
            'remarks'      => $_POST['remarks'],
            'items'        => $_POST['items']
        ];
        $id = $manager->transferStockToVan($transferData);
        header("Location: stock_transfer.php?voucher_id=$id&msg=success");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch master data for dropdowns
$warehouses = $pdo->query("SELECT * FROM warehouses")->fetchAll();
$vans = $pdo->query("SELECT * FROM vans")->fetchAll();
$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Transfer - Van Sales ERP</title>
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

    <?php if ($voucher): ?>
        <!-- Display Stock Transfer Voucher -->
        <div class="card shadow mb-4 border-success">
            <div class="card-header bg-success text-white d-flex justify-content-between">
                <h5 class="mb-0">Stock Transfer Voucher</h5>
                <strong>#<?= htmlspecialchars($voucher['reference_no']) ?></strong>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <small class="text-muted">From Warehouse:</small><br>
                        <strong><?= htmlspecialchars($voucher['warehouse_name']) ?></strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">To Van:</small><br>
                        <strong><?= htmlspecialchars($voucher['van_number']) ?></strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">Transfer Date:</small><br>
                        <strong><?= htmlspecialchars($voucher['transfer_date']) ?></strong>
                    </div>
                </div>

                <table class="table table-bordered table-sm">
                    <thead class="bg-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($voucher['items'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="text-end"><?= number_format($item['quantity'], 3) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="text-muted small">Remarks: <?= htmlspecialchars($voucher['remarks']) ?></p>
                <a href="stock_transfer.php" class="btn btn-outline-primary btn-sm">New Transfer</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stock Transfer Form -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">New Stock Transfer (Main Warehouse to Van)</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">From Warehouse</label>
                        <select name="warehouse_id" class="form-select" required>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">To Van</label>
                        <select name="van_id" class="form-select" required>
                            <?php foreach ($vans as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['van_number']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control">
                    </div>
                </div>

                <h6>Transfer Items</h6>
                <div id="transferItemsContainer">
                    <div class="row g-2 align-items-end mb-2 item-row">
                        <div class="col-md-7">
                            <label class="form-label small">Product</label>
                            <select name="items[0][product_id]" class="form-select" required>
                                <option value="">Select Product...</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Quantity</label>
                            <input type="number" name="items[0][quantity]" step="0.001" class="form-control" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addItemBtn">+ Add More Products</button>
                    <button type="submit" class="btn btn-primary">Submit Stock Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let itemCount = 1;
    const productsOptions = `<?= str_replace(["\r", "\n"], '', addslashes(
        implode('', array_map(fn($p) => "<option value='{$p['id']}'>".htmlspecialchars($p['name'])."</option>", $products))
    )) ?>`;

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('transferItemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 align-items-end mb-2 item-row';
        newRow.innerHTML = `
            <div class="col-md-7">
                <select name="items[\${itemCount}][product_id]" class="form-select" required>
                    <option value="">Select Product...</option>
                    \${productsOptions}
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[\${itemCount}][quantity]" step="0.001" class="form-control" required>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
            </div>
        `;
        container.appendChild(newRow);
        itemCount++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });
</script>

</body>
</html>
