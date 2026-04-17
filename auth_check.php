<?php
/**
 * auth_check.php
 *
 * Reusable Session Protection Middleware.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // Determine relative path to login.php
    $currentPath = $_SERVER['PHP_SELF'];
    $isNested = (str_contains($currentPath, '/admin/') || str_contains($currentPath, '/salesman/'));
    $prefix = $isNested ? '../' : '';
    header("Location: {$prefix}login.php?error=unauthorized");
    exit;
}

function requireRole($allowed_roles) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        die("<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p>");
    }
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    $currentPath = $_SERVER['PHP_SELF'];
    $isNested = (str_contains($currentPath, '/admin/') || str_contains($currentPath, '/salesman/'));
    $prefix = $isNested ? '../' : '../'; // Always go back if logging out from nested or root (simplified)
    header("Location: login.php?msg=loggedout"); // Default to root login.php
    exit;
}
