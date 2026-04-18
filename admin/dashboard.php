<?php
/**
 * admin/dashboard.php
 */
require_once '../includes/header.php';
requireRole(['Super Admin', 'Accountant']);
?>

<div class="page-header">
    <div class="container-fluid">
        <h1 class="h3 mb-0">Dashboard Overview</h1>
        <p class="text-muted">Welcome to the central command of your distribution business.</p>
    </div>
</div>

<div class="container-fluid px-4">
    <!-- STATS ROW (Mock data for UI) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase">Today's Sales</h6>
                        <h3 class="mb-0">₹0.00</h3>
                    </div>
                    <i class="bi bi-graph-up text-primary fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase">Pending Orders</h6>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <i class="bi bi-clock-history text-warning fs-2"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase">Active Vans</h6>
                        <h3 class="mb-0">1</h3>
                    </div>
                    <i class="bi bi-truck text-success fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">Recent Transactions</div>
                <div class="card-body py-5 text-center text-muted italic">
                    <i class="bi bi-receipt-cutoff fs-1 d-block mb-3"></i>
                    No recent activities recorded today.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
