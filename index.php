<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /pages/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSMS — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">

<div class="login-wrapper">
    <div class="login-card">

        <!-- Logo -->
        <div class="login-logo">
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" aria-label="DSMS Logo">
                <rect width="48" height="48" rx="12" fill="#01696f"/>
                <circle cx="24" cy="20" r="7" fill="white"/>
                <rect x="10" y="32" width="28" height="5" rx="2.5" fill="white"/>
                <circle cx="14" cy="37" r="3" fill="#01696f"/>
                <circle cx="34" cy="37" r="3" fill="#01696f"/>
            </svg>
            <h1 class="login-title">DSMS</h1>
            <p class="login-subtitle">Driving School Management System</p>
        </div>

        <!-- Alert Box -->
        <div id="loginAlert" class="alert d-none" role="alert"></div>

        <!-- Login Form -->
        <form id="loginForm" action="php/auth/login.php" method="POST" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="admin@dsms.com" required autocomplete="email">
                </div>
                <div class="invalid-feedback" id="emailError"></div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Enter your password" required autocomplete="current-password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="passwordError"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 login-btn" id="loginBtn">
                <span id="loginBtnText">Sign In</span>
                <span id="loginSpinner" class="spinner-border spinner-border-sm d-none ms-2"></span>
            </button>
        </form>

        <p class="login-footer">Driving School Management &copy; <?= date('Y') ?></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/validation.js"></script>
<script src="assets/js/ajax.js"></script>
</body>
</html>