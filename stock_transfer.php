<?php
require_once 'includes/header.php';
require_once 'includes/config.php';
requireRole(['Super Admin', 'Accountant']);
require_once 'includes/InventoryManager.php';

$manager = new InventoryManager($pdo);
$transferId = $_GET['voucher_id'] ?? null;
$voucher = $transferId ? $manager->getTransferVoucher($transferId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $manager->transferStockToVan($_POST);
        header("Location: stock_transfer.php?voucher_id=$id&msg=success");
        exit;
    } catch (Exception $e) { $error = $e->getMessage(); }
}

$warehouses = $pdo->query("SELECT * FROM warehouses")->fetchAll();
$vans = $pdo->query("SELECT * FROM vans")->fetchAll();
$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>

<div class="page-header">
    <div class="container-fluid">
        <h1 class="h3 mb-0">Stock Transfer</h1>
        <p class="text-muted">Move inventory from main warehouse to field vans.</p>
    </div>
</div>

<div class="container-fluid px-4">
    <?php if (isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">New Transfer</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small">From Warehouse</label>
                                <select name="warehouse_id" class="form-select" required>
                                    <?php foreach ($warehouses as $w): ?><option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['name']) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">To Van</label>
                                <select name="van_id" class="form-select" required>
                                    <?php foreach ($vans as $v): ?><option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['van_number']) ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div id="transferItemsContainer">
                            <div class="row g-2 mb-2 item-row align-items-end">
                                <div class="col-8">
                                    <label class="form-label small">Product</label>
                                    <select name="items[0][product_id]" class="form-select" required>
                                        <option value="">Select...</option>
                                        <?php foreach ($products as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label small">Qty</label>
                                    <input type="number" name="items[0][quantity]" step="0.001" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-link btn-sm text-primary p-0 mt-2" id="addItemBtn">+ Add more items</button>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label small">Remarks</label>
                            <input type="text" name="remarks" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Process Transfer</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($voucher): ?>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm border-top border-4 border-success">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Transfer Voucher</h6>
                    <p class="small mb-1 text-muted">Ref: <?= htmlspecialchars($voucher['reference_no']) ?></p>
                    <p class="small mb-3">Date: <?= $voucher['transfer_date'] ?></p>
                    <table class="table table-sm small">
                        <?php foreach ($voucher['items'] as $item): ?>
                        <tr><td><?= htmlspecialchars($item['product_name']) ?></td><td class="text-end"><?= number_format($item['quantity'], 3) ?></td></tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    let itemCount = 1;
    const prodOpts = `<?= str_replace(["\r", "\n"], '', addslashes(implode('', array_map(fn($p) => "<option value='{$p['id']}'>".htmlspecialchars($p['name'])."</option>", $products)))) ?>`;
    document.getElementById('addItemBtn').addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'row g-2 mb-2 item-row';
        div.innerHTML = `<div class='col-8'><select name='items[\${itemCount}][product_id]' class='form-select' required><option value=''>Select...</option>\${prodOpts}</select></div><div class='col-4'><input type='number' name='items[\${itemCount}][quantity]' step='0.001' class='form-control' required></div>`;
        document.getElementById('transferItemsContainer').appendChild(div);
        itemCount++;
    });
</script>

<?php require_once 'includes/footer.php'; ?>
