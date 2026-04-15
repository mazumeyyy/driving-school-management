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
    <title>DSMS — Students</title>
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
        <h2>Students</h2>
        <button class="btn text-white" style="background:var(--primary);" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="bi bi-plus-lg"></i> Add Student
        </button>
    </div>

    <!-- Alert -->
    <div id="studentAlert" class="alert d-none"></div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="studentsTable">
                <thead style="background:var(--bg);">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>License</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentsBody">
                    <tr><td colspan="7" class="text-center py-4 text-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--primary);">
                <h5 class="modal-title text-white">Add New Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addStudentForm">
                    <div class="mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" name="dob">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">License Type</label>
                            <select class="form-select" name="license_type">
                                <option value="A">A — Motorcycle/Scooter/Moped</option>
                                <option value="K">K — Scooter (specific)</option>
                                <option value="B">B — Private Car/Jeep/Van</option>
                                <option value="C">C — Tempo/Auto Rickshaw</option>
                                <option value="C1">C1 — E-Rickshaw</option>
                                <option value="D">D — Heavy Vehicle (Truck/Bus)</option>
                                <option value="E">E — Tractor/Trailer</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:var(--primary);" onclick="submitAddStudent()">Add Student</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Load students on page load
document.addEventListener('DOMContentLoaded', loadStudents);

function loadStudents() {
    fetch('/php/student/read.php')
    .then(r => r.json())
    .then(data => {
        const tbody = document.getElementById('studentsBody');
        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No students yet. Add one!</td></tr>';
            return;
        }
        tbody.innerHTML = data.map((s, i) => `
            <tr>
                <td>${i+1}</td>
                <td>${s.full_name}</td>
                <td>${s.email || '-'}</td>
                <td>${s.phone || '-'}</td>
                <td><span class="badge bg-secondary">${s.license_type}</span></td>
                <td><span class="badge ${s.status === 'active' ? 'bg-success' : s.status === 'completed' ? 'bg-primary' : 'bg-danger'}">${s.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent(${s.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    });
}

function submitAddStudent() {
    const form = document.getElementById('addStudentForm');
    const formData = new FormData(form);
    const alert = document.getElementById('studentAlert');

    fetch('/php/student/create.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        alert.className = `alert alert-${data.status === 'success' ? 'success' : 'danger'}`;
        alert.textContent = data.message;
        alert.classList.remove('d-none');
        if (data.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
            form.reset();
            loadStudents();
        }
    });
}

function deleteStudent(id) {
    if (!confirm('Delete this student?')) return;
    fetch('/php/student/delete.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') loadStudents();
    });
}
</script>
</body>
</html>

