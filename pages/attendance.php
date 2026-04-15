<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DSMS — Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body style="background: var(--bg);">

<nav class="navbar navbar-dark px-3" style="background: var(--primary);">
    <a class="navbar-brand fw-bold" href="/pages/dashboard.php">DSMS</a>
    <span class="text-white">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
    <a href="/php/auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Attendance</h2>
        <button class="btn text-white" style="background:var(--primary);" data-bs-toggle="modal" data-bs-target="#markAttendanceModal">
            <i class="bi bi-plus-lg"></i> Mark Attendance
        </button>
    </div>

    <div id="attendanceAlert" class="alert d-none"></div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead style="background:var(--bg);">
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Session Date</th>
                        <th>Status</th>
                        <th>Marked At</th>
                    </tr>
                </thead>
                <tbody id="attendanceBody">
                    <tr><td colspan="5" class="text-center py-4 text-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mark Attendance Modal -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--primary);">
                <h5 class="modal-title text-white">Mark Attendance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="markAttendanceForm">
                    <div class="mb-3">
                        <label class="form-label">Session *</label>
                        <select class="form-select" name="session_id" id="sessionSelect" required>
                            <option value="">-- Select Session --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student *</label>
                        <select class="form-select" name="student_id" id="attendanceStudentSelect" required>
                            <option value="">-- Select Student --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:var(--primary);" onclick="submitAttendance()">Mark</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    loadAttendance();
    loadSessionsDropdown();
    loadStudentsDropdown();
});

function loadSessionsDropdown() {
    fetch('/php/session/read.php')
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('sessionSelect');
        data.forEach(s => {
            select.innerHTML += `<option value="${s.id}">${s.student_name} — ${s.session_date}</option>`;
        });
    });
}

function loadStudentsDropdown() {
    fetch('/php/student/read.php')
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('attendanceStudentSelect');
        data.forEach(s => {
            select.innerHTML += `<option value="${s.id}">${s.full_name}</option>`;
        });
    });
}

function loadAttendance() {
    fetch('/php/attendance/read.php')
    .then(r => r.json())
    .then(data => {
        const tbody = document.getElementById('attendanceBody');
        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No attendance records yet.</td></tr>';
            return;
        }
        tbody.innerHTML = data.map((a, i) => `
            <tr>
                <td>${i+1}</td>
                <td>${a.student_name}</td>
                <td>${a.session_date}</td>
                <td><span class="badge ${a.status === 'present' ? 'bg-success' : a.status === 'late' ? 'bg-warning text-dark' : 'bg-danger'}">${a.status}</span></td>
                <td>${a.marked_at}</td>
            </tr>
        `).join('');
    });
}

function submitAttendance() {
    const formData = new FormData(document.getElementById('markAttendanceForm'));
    const alert = document.getElementById('attendanceAlert');

    fetch('/php/attendance/mark.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        alert.className = `alert alert-${data.status === 'success' ? 'success' : 'danger'}`;
        alert.textContent = data.message;
        alert.classList.remove('d-none');
        if (data.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('markAttendanceModal')).hide();
            document.getElementById('markAttendanceForm').reset();
            loadAttendance();
        }
    });
}
</script>
</body>
</html><?php
