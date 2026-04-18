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
        .icon-box { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px; margin-bottom: 0.8rem; }
        .section-title { border-left: 4px solid #007bff; padding-left: 10px; margin-bottom: 1.5rem; }
    </style>
</head>
<body class="bg-light pb-5">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Van Sales ERP</a>
        <div class="d-flex align-items-center">
            <span class="text-white-50 me-3 small">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="../auth_check.php?logout=1" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">

    <!-- ACCOUNTING SECTION -->
    <h4 class="section-title">Accounts & Finance</h4>
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <a href="../coa.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center">
                <div class="icon-box bg-primary text-white mx-auto"><i class="bi bi-diagram-3 fs-4"></i></div>
                <h6 class="mb-0">Chart of Accounts</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="../vouchers.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center">
                <div class="icon-box bg-success text-white mx-auto"><i class="bi bi-receipt fs-4"></i></div>
                <h6 class="mb-0">Manual Vouchers</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="day_book.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center">
                <div class="icon-box bg-dark text-white mx-auto"><i class="bi bi-journal-text fs-4"></i></div>
                <h6 class="mb-0">Day Book</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="../trial_balance.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center">
                <div class="icon-box bg-info text-white mx-auto"><i class="bi bi-calculator fs-4"></i></div>
                <h6 class="mb-0">Trial Balance</h6>
            </a>
        </div>
    </div>

    <!-- SALES & INVENTORY SECTION -->
    <h4 class="section-title">Sales & Operations</h4>
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <a href="../stock_transfer.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center">
                <div class="icon-box bg-warning text-dark mx-auto"><i class="bi bi-truck fs-4"></i></div>
                <h6 class="mb-0">Stock Transfer</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="../gstr1_b2b.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center">
                <div class="icon-box bg-danger text-white mx-auto"><i class="bi bi-file-earmark-bar-graph fs-4"></i></div>
                <h6 class="mb-0">GSTR-1 Report</h6>
            </a>
        </div>
        <?php if ($_SESSION['role'] === 'Super Admin'): ?>
        <div class="col-6 col-md-3">
            <a href="../salesman/pos_terminal.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center">
                <div class="icon-box bg-secondary text-white mx-auto"><i class="bi bi-pc-display fs-4"></i></div>
                <h6 class="mb-0">POS Terminal</h6>
            </a>
        </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>
