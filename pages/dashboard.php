<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DSMS — Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark" style="background: var(--primary);">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">DSMS</span>
            <span class="text-white">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="/php/auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Dashboard</h2>
        <p class="text-muted">Welcome to the Driving School Management System.</p>

        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Students</h4>
                    <p class="text-muted">Manage students</p>
                    <a href="/pages/students.php" class="btn btn-sm login-btn text-white">View</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Sessions</h4>
                    <p class="text-muted">Book sessions</p>
                    <a href="/pages/sessions.php" class="btn btn-sm login-btn text-white">View</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Attendance</h4>
                    <p class="text-muted">Mark attendance</p>
                    <a href="/pages/attendance.php" class="btn btn-sm login-btn text-white">View</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3">
                    <h4>Certificates</h4>
                    <p class="text-muted">Generate certificates</p>
                    <a href="/pages/certificate.php" class="btn btn-sm login-btn text-white">View</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

