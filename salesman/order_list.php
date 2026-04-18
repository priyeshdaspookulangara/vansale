<?php
/**
 * salesman/order_list.php
 */
require_once '../includes/header.php';
requireRole(['Van Salesman', 'Super Admin']);
require_once '../includes/config.php';
require_once '../includes/OrderManager.php';

$manager = new OrderManager($pdo);
$vanId = $_SESSION['assigned_van_id'] ?? 1;
$orders = $manager->getPendingOrders($vanId);
?>

<div class="page-header">
    <div class="container-fluid">
        <h1 class="h3 mb-0">Pending Orders</h1>
        <p class="text-muted">Track and fulfill pre-booked customer orders.</p>
    </div>
</div>

<div class="container-fluid px-4">
    <?php if (empty($orders)): ?>
        <div class="card border-0 shadow-sm p-5 text-center text-muted">
            <i class="bi bi-inbox fs-1 mb-3"></i>
            <p>No pending orders for your van at the moment.</p>
            <a href="pre_order.php" class="btn btn-primary btn-sm mx-auto" style="max-width: 200px;">Book New Order</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($orders as $o): ?>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h6 class="card-title fw-bold mb-0"><?= htmlspecialchars($o['customer_name']) ?></h6>
                                <span class="badge bg-warning text-dark small">Pending</span>
                            </div>
                            <div class="mb-3 small">
                                <div class="text-muted"><i class="bi bi-calendar-event me-2"></i>Date: <?= $o['order_date'] ?></div>
                                <div class="text-dark fw-bold mt-2"><i class="bi bi-currency-rupee me-1"></i><?= number_format($o['total_amount'], 2) ?></div>
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-success btn-sm"><i class="bi bi-check2-circle me-2"></i>Fulfill Order</button>
                                <button class="btn btn-outline-secondary btn-sm">View Items</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
