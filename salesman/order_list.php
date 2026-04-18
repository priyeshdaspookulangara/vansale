<?php
require_once '../auth_check.php';
requireRole(['Van Salesman', 'Super Admin']);
require_once '../includes/config.php';
require_once '../includes/OrderManager.php';

$manager = new OrderManager($pdo);
$vanId = $_SESSION['assigned_van_id'] ?? 1;
$orders = $manager->getPendingOrders($vanId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <span class="navbar-brand">Pending Orders</span>
        <a href="pos_terminal.php" class="btn btn-outline-light btn-sm">POS</a>
    </div>
</nav>

<div class="container">
    <?php if (empty($orders)): ?>
        <div class="text-center mt-5 text-muted">No pending orders for this van.</div>
    <?php else: ?>
        <?php foreach ($orders as $o): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h6 class="card-title"><?= htmlspecialchars($o['customer_name']) ?></h6>
                        <span class="badge bg-warning text-dark">Pending</span>
                    </div>
                    <p class="small text-muted mb-2">Order Date: <?= $o['order_date'] ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>₹<?= number_format($o['total_amount'], 2) ?></strong>
                        <button class="btn btn-sm btn-success">Fulfill Now</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
