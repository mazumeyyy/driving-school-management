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
    <title>DSMS — Sessions</title>
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
            <h2>Sessions</h2>
            <button class="btn text-white" style="background:var(--primary);" data-bs-toggle="modal" data-bs-target="#addSessionModal">
                <i class="bi bi-plus-lg"></i> Book Session
            </button>
        </div>

        <div id="sessionAlert" class="alert d-none"></div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead style="background:var(--bg);">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Slot</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sessionsBody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Book Session Modal -->
    <div class="modal fade" id="addSessionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background:var(--primary);">
                    <h5 class="modal-title text-white">Book New Session</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSessionForm">
                        <div class="mb-3">
                            <label class="form-label">Student *</label>
                            <select class="form-select" name="student_id" id="studentSelect" required>
                                <option value="">-- Select Student --</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Session Date *</label>
                            <input type="date" class="form-control" name="session_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Session Slot *</label>
                            <select class="form-select" name="slot" required>
                                <option value="">-- Select Slot --</option>
                                <option value="Morning (6:00 AM - 10:00 AM)"> Morning (6:00 AM - 10:00 AM)</option>
                                <option value="Afternoon (11:00 AM - 3:00 PM)"> Afternoon (11:00 AM - 3:00 PM)</option>
                                <option value="Evening (4:00 PM - 7:00 PM)"> Evening (4:00 PM - 7:00 PM)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="scheduled">Scheduled</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn text-white" style="background:var(--primary);" onclick="submitAddSession()">Book Session</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadSessions();
            loadStudentsDropdown();
        });

        function loadStudentsDropdown() {
            fetch('/php/student/read.php')
                .then(r => r.json())
                .then(data => {
                    const select = document.getElementById('studentSelect');
                    data.forEach(s => {
                        select.innerHTML += `<option value="${s.id}">${s.full_name}</option>`;
                    });
                });
        }

        function loadSessions() {
            fetch('/php/session/read.php')
                .then(r => r.json())
                .then(data => {
                    const tbody = document.getElementById('sessionsBody');
                    if (!data.length) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No sessions yet. Book one!</td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.map((s, i) => `
            <tr>
                <td>${i+1}</td>
                <td>${s.student_name}</td>
                <td>${s.session_date}</td>
                <td>${s.slot || '-'}</td>
                <td><span class="badge ${s.status === 'scheduled' ? 'bg-warning text-dark' : s.status === 'completed' ? 'bg-success' : 'bg-danger'}">${s.status}</span></td>
                <td>${s.notes || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSession(${s.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
                });
        }

        function submitAddSession() {
            const formData = new FormData(document.getElementById('addSessionForm'));
            const alert = document.getElementById('sessionAlert');

            fetch('/php/session/book.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    alert.className = `alert alert-${data.status === 'success' ? 'success' : 'danger'}`;
                    alert.textContent = data.message;
                    alert.classList.remove('d-none');
                    if (data.status === 'success') {
                        bootstrap.Modal.getInstance(document.getElementById('addSessionModal')).hide();
                        document.getElementById('addSessionForm').reset();
                        loadSessions();
                    }
                });
        }

        function deleteSession(id) {
            if (!confirm('Delete this session?')) return;
            fetch('/php/session/delete.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        id
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') loadSessions();
                });
        }
    </script>
</body>

</html>