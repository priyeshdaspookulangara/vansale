<?php
/**
 * auth_check.php
 *
 * Reusable Session Protection Middleware.
 * Included at the top of every secured page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is logged in.
 * If not, kick them back to login.php.
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=unauthorized");
    exit;
}

/**
 * Require a specific role to access the page.
 *
 * Usage: requireRole(['Super Admin', 'Accountant']);
 *
 * @param array $allowed_roles
 */
function requireRole($allowed_roles) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // Log unauthorized attempt?
        die("<h1>403 Forbidden</h1><p>You do not have permission to access this page.</p><a href='/index.php'>Go Back</a>");
    }
}

/**
 * Logout logic (can be required here or in a separate file).
 */
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php?msg=loggedout");
    exit;
}
