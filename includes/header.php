<?php
/**
 * includes/header.php
 *
 * Shared Header with Sidebar Layout Support.
 */
require_once __DIR__ . '/../auth_check.php';

// Helper to get relative root path
$currentPath = $_SERVER['PHP_SELF'];
$isNested = (str_contains($currentPath, '/admin/') || str_contains($currentPath, '/salesman/'));
$prefix = $isNested ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Van Sales ERP - Professional Edition</title>

    <meta name="robots" content="noindex">

    <!-- App CSS & Icons (Using CDN for reliability in demo) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root { --sidebar-width: 260px; }
        body { background-color: #f4f7fb; overflow-x: hidden; }
        #wrapper { display: flex; width: 100%; align-items: stretch; }

        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: #fff;
            color: #333;
            transition: all 0.3s;
            border-right: 1px solid #e0e0e0;
            min-height: 100vh;
            z-index: 1000;
        }

        #content { width: 100%; padding: 0; min-height: 100vh; }

        .sidebar-header { padding: 20px; background: #fff; border-bottom: 1px solid #f0f0f0; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-item a {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #616161;
            font-size: 0.9rem;
            border-left: 3px solid transparent;
        }
        .sidebar-item a:hover { background: #f8f9fa; color: #007bff; border-left-color: #007bff; }
        .sidebar-item.active a { background: #e7f1ff; color: #007bff; border-left-color: #007bff; font-weight: 600; }
        .sidebar-item i { margin-right: 12px; font-size: 20px; }

        .sidebar-heading { padding: 20px 20px 10px; text-transform: uppercase; font-size: 0.7rem; font-weight: 700; color: #9e9e9e; letter-spacing: 1px; }

        .navbar-main { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 0.5rem 1.5rem; }
        .page-header { background: #fff; padding: 1.5rem; border-bottom: 1px solid #e0e0e0; margin-bottom: 1.5rem; }

        @media (max-width: 992px) {
            #sidebar { margin-left: calc(-1 * var(--sidebar-width)); position: fixed; height: 100%; }
            #sidebar.active { margin-left: 0; }
        }
    </style>
</head>
<body>

<div id="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header d-flex align-items-center">
            <img src="<?= $prefix ?>images/stack-logo-blue.svg" width="22" class="me-2" onerror="this.src='https://via.placeholder.com/22/007bff/ffffff?text=S'">
            <span class="fw-bold text-primary">VAN SALES ERP</span>
        </div>

        <div class="sidebar-heading">Main Menu</div>
        <ul class="sidebar-menu">
            <?php if (in_array($_SESSION['role'], ['Super Admin', 'Accountant'])): ?>
                <li class="sidebar-item <?= str_contains($currentPath, 'dashboard.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>admin/dashboard.php"><i class="material-icons">dashboard</i> Dashboard</a>
                </li>

                <div class="sidebar-heading">Master Data</div>
                <li class="sidebar-item <?= str_contains($currentPath, 'products.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>admin/products.php"><i class="material-icons">inventory_2</i> Products Master</a>
                </li>
                <li class="sidebar-item <?= str_contains($currentPath, 'categories.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>admin/categories.php"><i class="material-icons">category</i> Categories</a>
                </li>

                <div class="sidebar-heading">Accounts & Finance</div>
                <li class="sidebar-item <?= str_contains($currentPath, 'coa.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>coa.php"><i class="material-icons">account_tree</i> Chart of Accounts</a>
                </li>
                <li class="sidebar-item <?= str_contains($currentPath, 'vouchers.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>vouchers.php"><i class="material-icons">receipt</i> Manual Vouchers</a>
                </li>
                <li class="sidebar-item <?= str_contains($currentPath, 'day_book.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>admin/day_book.php"><i class="material-icons">event_note</i> Day Book</a>
                </li>
                <li class="sidebar-item <?= str_contains($currentPath, 'trial_balance.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>trial_balance.php"><i class="material-icons">balance</i> Trial Balance</a>
                </li>

                <div class="sidebar-heading">Inventory & GST</div>
                <li class="sidebar-item <?= str_contains($currentPath, 'stock_transfer.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>stock_transfer.php"><i class="material-icons">local_shipping</i> Stock Transfer</a>
                </li>
                <li class="sidebar-item <?= str_contains($currentPath, 'gstr1_b2b.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>gstr1_b2b.php"><i class="material-icons">assessment</i> GSTR-1 (B2B)</a>
                </li>
            <?php endif; ?>

            <?php if (in_array($_SESSION['role'], ['Van Salesman', 'Super Admin'])): ?>
                <?php if ($_SESSION['role'] === 'Van Salesman'): ?>
                <div class="sidebar-heading">Field Sales</div>
                <?php endif; ?>
                <li class="sidebar-item <?= str_contains($currentPath, 'pos_terminal.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>salesman/pos_terminal.php"><i class="material-icons">point_of_sale</i> POS Terminal</a>
                </li>
                <li class="sidebar-item <?= str_contains($currentPath, 'pre_order.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>salesman/pre_order.php"><i class="material-icons">add_shopping_cart</i> Book Order</a>
                </li>
                <li class="sidebar-item <?= str_contains($currentPath, 'order_list.php') ? 'active' : '' ?>">
                    <a href="<?= $prefix ?>salesman/order_list.php"><i class="material-icons">list_alt</i> Pending Orders</a>
                </li>
            <?php endif; ?>
        </ul>

        <div class="mt-auto p-3 border-top">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-sm me-2 bg-light rounded-circle p-2"><i class="bi bi-person text-primary"></i></div>
                <div class="flex">
                    <div class="small fw-bold text-dark"><?= htmlspecialchars($_SESSION['username']) ?></div>
                    <div class="text-muted" style="font-size: 0.7rem;"><?= $_SESSION['role'] ?></div>
                </div>
            </div>
            <a href="<?= $prefix ?>auth_check.php?logout=1" class="btn btn-sm btn-outline-danger w-100 mt-3">Logout</a>
        </div>
    </nav>

    <!-- Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-main">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-light d-lg-none">
                    <i class="material-icons">menu</i>
                </button>

                <div class="ms-auto d-flex align-items-center">
                    <span class="text-muted small me-3"><?= date('l, d M Y') ?></span>
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown">
                             <i class="material-icons align-middle">notifications_none</i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
