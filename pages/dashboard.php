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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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

        <!-- Stats Row -->
        <div class="row mt-3 mb-4 g-3" id="statsRow">
            <div class="col-md-3">
                <div class="card text-center p-3 border-0 shadow-sm">
                    <div class="mb-2" style="color:var(--primary); font-size:2rem;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-0" id="statStudents">—</h3>
                    <p class="text-muted mb-0">Total Students</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 border-0 shadow-sm">
                    <div class="mb-2" style="color:var(--primary); font-size:2rem;">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-0" id="statSessions">—</h3>
                    <p class="text-muted mb-0">Total Sessions</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 border-0 shadow-sm">
                    <div class="mb-2" style="color:var(--primary); font-size:2rem;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-0" id="statAttendance">—</h3>
                    <p class="text-muted mb-0">Today's Attendance</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center p-3 border-0 shadow-sm">
                    <div class="mb-2" style="color:var(--primary); font-size:2rem;">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-0" id="statCertificates">—</h3>
                    <p class="text-muted mb-0">Certificates Issued</p>
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="row mt-2 g-3">
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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];

        // Total Students
        fetch('/php/student/read.php')
            .then(r => r.json())
            .then(data => {
                document.getElementById('statStudents').textContent = data.length;
            });

        // Total Sessions
        fetch('/php/session/read.php')
            .then(r => r.json())
            .then(data => {
                document.getElementById('statSessions').textContent = data.length;
            });

        // Today's Attendance
        fetch('/php/attendance/read.php')
            .then(r => r.json())
            .then(data => {
                const todayCount = data.filter(a => a.session_date === today).length;
                document.getElementById('statAttendance').textContent = todayCount;
            });

        // Certificates
        fetch('/php/certificate/read.php')
            .then(r => r.json())
            .then(data => {
                document.getElementById('statCertificates').textContent = data.length;
            })
            .catch(() => {
                document.getElementById('statCertificates').textContent = '0';
            });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

