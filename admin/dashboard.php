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
    <!-- MASTER DATA SECTION -->
    <h4 class="section-title fw-bold small text-uppercase text-muted mb-3" style="letter-spacing: 1px;">Master Data</h4>
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <a href="products.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center text-decoration-none">
                <div class="icon-box bg-dark text-white mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px;">
                    <i class="bi bi-box-seam fs-4"></i>
                </div>
                <h6 class="mb-0 text-dark">Products Master</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="categories.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center text-decoration-none">
                <div class="icon-box bg-secondary text-white mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px;">
                    <i class="bi bi-tags fs-4"></i>
                </div>
                <h6 class="mb-0 text-dark">Categories</h6>
            </a>
        </div>
    </div>

    <!-- ACCOUNTING SECTION -->
    <h4 class="section-title fw-bold small text-uppercase text-muted mb-3" style="letter-spacing: 1px;">Accounts & Finance</h4>
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <a href="../coa.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center text-decoration-none">
                <div class="icon-box bg-primary text-white mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px;">
                    <i class="bi bi-diagram-3 fs-4"></i>
                </div>
                <h6 class="mb-0 text-dark">Chart of Accounts</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="../vouchers.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center text-decoration-none">
                <div class="icon-box bg-success text-white mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
                <h6 class="mb-0 text-dark">Manual Vouchers</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="day_book.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center text-decoration-none">
                <div class="icon-box bg-dark text-white mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px;">
                    <i class="bi bi-journal-text fs-4"></i>
                </div>
                <h6 class="mb-0 text-dark">Day Book</h6>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="../trial_balance.php" class="card card-menu h-100 border-0 shadow-sm p-3 text-center text-decoration-none">
                <div class="icon-box bg-info text-white mx-auto d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px;">
                    <i class="bi bi-calculator fs-4"></i>
                </div>
                <h6 class="mb-0 text-dark">Trial Balance</h6>
            </a>
        </div>
    </div>
</div>

<style>
    .card-menu { transition: transform 0.2s; }
    .card-menu:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
    .section-title { border-left: 4px solid #007bff; padding-left: 10px; }
</style>

<?php require_once '../includes/footer.php'; ?>
