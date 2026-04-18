<?php
require_once '../auth_check.php';
requireRole(['Van Salesman', 'Super Admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Van Sales ERP - Spot Sale</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { margin-bottom: 1rem; }
        .item-row { border-bottom: 1px solid #dee2e6; padding: 10px 0; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Van Sales POS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="pos_terminal.php">Spot Sale</a></li>
                <li class="nav-item"><a class="nav-link" href="pre_order.php">Book Order</a></li>
                <li class="nav-item"><a class="nav-link" href="order_list.php">Pending Orders</a></li>
            </ul>
            <div class="navbar-text text-white-50 me-3 small">Salesman: <?= htmlspecialchars($_SESSION['username']) ?></div>
            <a href="../auth_check.php?logout=1" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <form id="saleForm" method="POST" action="../process_sale.php">
        <!-- Customer & Van Selection -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Customer & Van Info</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">Select Customer...</option>
                            <option value="1">Local B2B Customer (Maharashtra)</option>
                            <option value="2">Inter-state B2B Customer (Tamil Nadu)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="van_id" class="form-label">Van</label>
                        <select class="form-select" id="van_id" name="van_id" required>
                            <option value="1">MH-12-AB-1234</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment_mode" class="form-label">Payment Mode</label>
                        <select class="form-select" id="payment_mode" name="payment_mode" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit">Credit (Outstanding)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Products</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">+ Add Item</button>
                </div>

                <div id="itemsContainer">
                    <!-- Dynamic items will be added here -->
                    <div class="item-row row g-2 align-items-end">
                        <div class="col-6 col-md-5">
                            <label class="form-label small">Product</label>
                            <select class="form-select form-select-sm" name="items[0][product_id]" required>
                                <option value="1">Wheat Flour 5kg (Base: 250.00)</option>
                            </select>
                        </div>
                        <div class="col-3 col-md-2">
                            <label class="form-label small">Qty</label>
                            <input type="number" step="0.001" class="form-control form-select-sm" name="items[0][quantity]" value="1" required>
                        </div>
                        <div class="col-3 col-md-3">
                            <label class="form-label small">Unit Price</label>
                            <input type="number" step="0.01" class="form-control form-select-sm" name="items[0][unit_price]" value="250.00" required>
                        </div>
                        <div class="col-12 col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid gap-2 mb-5">
            <button type="submit" class="btn btn-success btn-lg">Submit Spot Sale</button>
        </div>
    </form>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let itemCount = 1;

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'item-row row g-2 align-items-end';
        newRow.innerHTML = `
            <div class="col-6 col-md-5">
                <label class="form-label small">Product</label>
                <select class="form-select form-select-sm" name="items[${itemCount}][product_id]" required>
                    <option value="1">Wheat Flour 5kg (Base: 250.00)</option>
                </select>
            </div>
            <div class="col-3 col-md-2">
                <label class="form-label small">Qty</label>
                <input type="number" step="0.001" class="form-control form-select-sm" name="items[${itemCount}][quantity]" value="1" required>
            </div>
            <div class="col-3 col-md-3">
                <label class="form-label small">Unit Price</label>
                <input type="number" step="0.01" class="form-control form-select-sm" name="items[${itemCount}][unit_price]" value="250.00" required>
            </div>
            <div class="col-12 col-md-2 text-end">
                <button type="button" class="btn btn-sm btn-danger remove-item">Remove</button>
            </div>
        `;
        container.appendChild(newRow);
        itemCount++;
    });

    document.getElementById('itemsContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });
</script>

</body>
</html>
