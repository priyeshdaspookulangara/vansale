<?php
/**
 * salesman/pos_terminal.php
 */
require_once '../includes/header.php';
requireRole(['Van Salesman', 'Super Admin']);
?>

<div class="page-header">
    <div class="container-fluid">
        <h1 class="h3 mb-0">POS Terminal</h1>
        <p class="text-muted">Process immediate spot sales for your van.</p>
    </div>
</div>

<div class="container-fluid px-4">
    <form id="saleForm" method="POST" action="../process_sale.php">
        <div class="row">
            <!-- Left Side: Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Items Selection</h6>
                        <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">+ Add Item</button>
                    </div>
                    <div class="card-body" id="itemsContainer">
                        <div class="item-row row g-2 align-items-end mb-3">
                            <div class="col-md-5">
                                <label class="form-label small">Product</label>
                                <select class="form-select" name="items[0][product_id]" required>
                                    <option value="1">Wheat Flour 5kg (Base: 250.00)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Qty</label>
                                <input type="number" step="0.001" class="form-control" name="items[0][quantity]" value="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Unit Price</label>
                                <input type="number" step="0.01" class="form-control" name="items[0][unit_price]" value="250.00" required>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="material-icons fs-6">delete</i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Meta & Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white fw-bold">Sale Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small">Customer</label>
                            <select class="form-select" name="customer_id" required>
                                <option value="">Select Customer...</option>
                                <option value="1">Local B2B Customer (Maharashtra)</option>
                                <option value="2">Inter-state B2B Customer (Tamil Nadu)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Van</label>
                            <select class="form-select" name="van_id" required>
                                <option value="1">MH-12-AB-1234</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small">Payment</label>
                            <select class="form-select" name="payment_mode" required>
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit">Credit (Outstanding)</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Finalize & Print</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let itemCount = 1;
    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'item-row row g-2 align-items-end mb-3';
        newRow.innerHTML = `
            <div class="col-md-5">
                <select class="form-select" name="items[\${itemCount}][product_id]" required>
                    <option value="1">Wheat Flour 5kg (Base: 250.00)</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.001" class="form-control" name="items[\${itemCount}][quantity]" value="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" step="0.01" class="form-control" name="items[\${itemCount}][unit_price]" value="250.00" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="material-icons fs-6">delete</i></button>
            </div>
        `;
        container.appendChild(newRow);
        itemCount++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>
