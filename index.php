<?php
/**
 * index.php
 *
 * Simple landing page / redirector.
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (in_array($_SESSION['role'], ['Super Admin', 'Accountant'])) {
    header("Location: admin/dashboard.php");
} else {
    header("Location: salesman/pos_terminal.php");
}
exit;
