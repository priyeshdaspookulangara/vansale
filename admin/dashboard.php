<?php
/**
 * admin/dashboard.php
 *
 * Central Admin & Accountant Dashboard.
 */

require_once '../auth_check.php';
requireRole(['Super Admin', 'Accountant']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card-menu { transition: transform 0.2s; cursor: pointer; text-decoration: none; color: inherit; }
        .card-menu:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .icon-box { width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 12px; margin-bottom: 1rem; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Van Sales ERP</a>
        <div class="d-flex align-items-center">
            <span class="text-white-50 me-3 small">Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= $_SESSION['role'] ?>)</span>
            <a href="../auth_check.php?logout=1" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4">Admin Control Panel</h2>

    <div class="row g-4">
        <!-- Masters & Configuration -->
        <div class="col-md-4">
            <a href="../coa.php" class="card card-menu h-100 border-0 shadow-sm p-4 text-center">
                <div class="icon-box bg-primary text-white mx-auto"><i class="bi bi-diagram-3 fs-3"></i></div>
                <h5>Chart of Accounts</h5>
                <p class="text-muted small">Manage account heads and financial hierarchy.</p>
            </a>
        </div>

        <!-- Inventory -->
        <div class="col-md-4">
            <a href="../stock_transfer.php" class="card card-menu h-100 border-0 shadow-sm p-4 text-center">
                <div class="icon-box bg-warning text-dark mx-auto"><i class="bi bi-truck fs-3"></i></div>
                <h5>Stock Transfer</h5>
                <p class="text-muted small">Move stock from Warehouse to field Vans.</p>
            </a>
        </div>

        <!-- Accounting -->
        <div class="col-md-4">
            <a href="../vouchers.php" class="card card-menu h-100 border-0 shadow-sm p-4 text-center">
                <div class="icon-box bg-success text-white mx-auto"><i class="bi bi-receipt fs-3"></i></div>
                <h5>Manual Vouchers</h5>
                <p class="text-muted small">Record Receipt, Payment, and Journal entries.</p>
            </a>
        </div>

        <!-- Reports -->
        <div class="col-md-4">
            <a href="../trial_balance.php" class="card card-menu h-100 border-0 shadow-sm p-4 text-center">
                <div class="icon-box bg-info text-white mx-auto"><i class="bi bi-calculator fs-3"></i></div>
                <h5>Trial Balance</h5>
                <p class="text-muted small">Verify accounting accuracy and balances.</p>
            </a>
        </div>

        <div class="col-md-4">
            <a href="../gstr1_b2b.php" class="card card-menu h-100 border-0 shadow-sm p-4 text-center">
                <div class="icon-box bg-danger text-white mx-auto"><i class="bi bi-file-earmark-bar-graph fs-3"></i></div>
                <h5>GSTR-1 (B2B)</h5>
                <p class="text-muted small">Tax compliance and B2B sales extracts.</p>
            </a>
        </div>
    </div>
</div>

</body>
</html>
