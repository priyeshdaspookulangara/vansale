<?php
require_once '../auth_check.php';
requireRole(['Van Salesman', 'Super Admin']);
require_once '../includes/config.php';

// Fetch customers and products for selection
$customers = $pdo->query("SELECT id, name FROM customers")->fetchAll();
$products = $pdo->query("SELECT id, name, base_price FROM products")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Order - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-dark bg-info mb-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1 text-dark">Book Pre-Order</span>
        <a href="pos_terminal.php" class="btn btn-outline-dark btn-sm">Back to POS</a>
    </div>
</nav>

<div class="container">
    <div class="alert alert-info py-2 small">
        <strong>Pre-Order:</strong> No stock will be deducted now. Use this for advance booking.
    </div>

    <form id="orderForm" method="POST" action="save_order.php">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Customer</label>
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

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>Items Requested</span>
                <button type="button" class="btn btn-sm btn-primary" id="addBtn">+ Add</button>
            </div>
            <div class="card-body" id="itemsContainer">
                <div class="row g-2 mb-2 item-row">
                    <div class="col-8">
                        <select name="items[0][product_id]" class="form-select" required>
                            <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4">
                        <input type="number" name="items[0][quantity]" class="form-control" placeholder="Qty" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-info btn-lg">Save Pre-Order</button>
        </div>
    </form>
</div>

<script>
    let count = 1;
    document.getElementById('addBtn').addEventListener('click', () => {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 item-row';
        row.innerHTML = `
            <div class="col-8">
                <select name="items[\${count}][product_id]" class="form-select" required>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
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

</body>
</html>
