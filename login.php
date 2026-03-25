<?php
/**
 * login.php
 *
 * Secure Login Interface with CSRF Protection.
 */

session_start();

// Simple CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Van Sales ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .login-card .card-header { background: #007bff; color: white; border-radius: 12px 12px 0 0; padding: 1.5rem; }
    </style>
</head>
<body>

<div class="login-card card border-0">
    <div class="card-header text-center">
        <h4 class="mb-0">Van Sales ERP</h4>
        <small>Enterprise Resource Planning</small>
    </div>
    <div class="card-body p-4">
        <?php if ($error === 'invalid'): ?>
            <div class="alert alert-danger p-2 small">Invalid username or password.</div>
        <?php elseif ($error === 'disabled'): ?>
            <div class="alert alert-warning p-2 small">Your account is disabled. Please contact admin.</div>
        <?php elseif ($error === 'unauthorized'): ?>
            <div class="alert alert-danger p-2 small">Unauthorized access. Please login.</div>
        <?php endif; ?>

        <form action="auth_process.php" method="POST">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="mb-3">
                <label for="login_field" class="form-label small">Email or Username</label>
                <input type="text" name="login_field" id="login_field" class="form-control" required autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
            </div>
        </form>
    </div>
    <div class="card-footer bg-white border-0 text-center pb-3">
        <p class="text-muted small mb-0">&copy; <?= date('Y') ?> Van Sales ERP. All rights reserved.</p>
    </div>
</div>

</body>
</html>
