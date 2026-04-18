<?php
/**
 * salesman/pre_order.php
 */
require_once '../includes/header.php';
requireRole(['Van Salesman', 'Super Admin']);
require_once '../includes/config.php';

// Fetch customers and products for selection
$customers = $pdo->query("SELECT id, name FROM customers")->fetchAll();
$products = $pdo->query("SELECT id, name, base_price FROM products")->fetchAll();
?>

<div class="page-header">
    <div class="container-fluid">
        <h1 class="h3 mb-0">Book Pre-Order</h1>
        <p class="text-muted">Take orders for future delivery. No inventory deduction now.</p>
    </div>
</div>

<div class="container-fluid px-4">
    <div class="alert alert-info border-0 shadow-sm py-2 small mb-4">
        <i class="bi bi-info-circle-fill me-2"></i> <strong>Pre-Order:</strong> No stock will be deducted now. Use this for advance booking.
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="orderForm" method="POST" action="save_order.php">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Customer</label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">Select Customer...</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="van_id" value="<?= $_SESSION['assigned_van_id'] ?>">
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Items Requested</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addBtn">+ Add Product</button>
                    </div>
                    <div class="card-body" id="itemsContainer">
                        <div class="row g-2 mb-3 item-row align-items-end">
                            <div class="col-8">
                                <label class="form-label small">Product</label>
                                <select name="items[0][product_id]" class="form-select" required>
                                    <option value="">Select...</option>
                                    <?php foreach ($products as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small">Quantity</label>
                                <input type="number" name="items[0][quantity]" class="form-control" placeholder="Qty" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-info btn-lg text-white">Save Pre-Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let count = 1;
    const prodOpts = `<?= str_replace(["\r", "\n"], '', addslashes(implode('', array_map(fn($p) => "<option value='{$p['id']}'>".htmlspecialchars($p['name'])."</option>", $products)))) ?>`;

    document.getElementById('addBtn').addEventListener('click', () => {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-3 item-row align-items-end';
        row.innerHTML = `
            <div class="col-8">
                <select name="items[\${count}][product_id]" class="form-select" required>
                    <option value="">Select...</option>
                    \${prodOpts}
                </select>
            </div>
            <div class="col-4">
                <input type="number" name="items[\${count}][quantity]" class="form-control" placeholder="Qty" required>
            </div>
        `;
        container.appendChild(row);
        count++;
    });
</script>

<?php require_once '../includes/footer.php'; ?>
