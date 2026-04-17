<?php
/**
 * auth_process.php
 *
 * Secure Backend for Authentication.
 * Handles:
 * - Password verification.
 * - CSRF verification.
 * - Session initialization.
 * - Role-based redirection.
 */

require 'includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF verification failed.");
    }

    $loginField = $_POST['login_field'] ?? '';
    $password = $_POST['password'] ?? '';

    // 2. Fetch User by Email or Username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (email = ? OR username = ?) LIMIT 1");
    $stmt->execute([$loginField, $loginField]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 3. Verify User and Password
    if ($user && password_verify($password, $user['password_hash'])) {
        // Account Status Check
        if (!$user['is_active']) {
            header("Location: login.php?error=disabled");
            exit;
        }

        // 4. Set Session Variables Securely
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['assigned_van_id'] = $user['assigned_van_id'];
        $_SESSION['last_activity'] = time();

        // 5. Role-Based Redirection
        if (in_array($user['role'], ['Super Admin', 'Accountant'])) {
            // Corrected paths for redirection
            header("Location: admin/dashboard.php");
        } elseif ($user['role'] === 'Van Salesman') {
            header("Location: salesman/pos_terminal.php");
        } else {
            // Default fallback
            header("Location: index.php");
        }
        exit;

    } else {
        // 6. Generic Error for Security (Don't reveal if email was correct or not)
        header("Location: login.php?error=invalid");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
