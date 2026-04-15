<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DSMS — Certificates</title>
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
        <h2>Certificates</h2>
        <button class="btn text-white" style="background:var(--primary);" data-bs-toggle="modal" data-bs-target="#issueCertModal">
            <i class="bi bi-plus-lg"></i> Issue Certificate
        </button>
    </div>

    <div id="certAlert" class="alert d-none"></div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead style="background:var(--bg);">
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>License Type</th>
                        <th>Issue Date</th>
                        <th>Certificate No.</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="certsBody">
                    <tr><td colspan="6" class="text-center py-4 text-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Issue Certificate Modal -->
<div class="modal fade" id="issueCertModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--primary);">
                <h5 class="modal-title text-white">Issue Certificate</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="issueCertForm">
                    <div class="mb-3">
                        <label class="form-label">Student *</label>
                        <select class="form-select" name="student_id" id="certStudentSelect" required>
                            <option value="">-- Select Student --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Issue Date *</label>
                        <input type="date" class="form-control" name="issue_date" required
                            value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Optional remarks..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" style="background:var(--primary);" onclick="submitIssueCert()">Issue</button>
            </div>
        </div>
    </div>
</div>

<!-- Print Certificate Modal -->
<div class="modal fade" id="printCertModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--primary);">
                <h5 class="modal-title text-white">Certificate Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="certPrintArea" style="padding: 60px; background: #fff; font-family: Georgia, serif; text-align: center;">
                    <!-- Filled dynamically -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn text-white" style="background:var(--primary);" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    loadCerts();
    loadStudentsDropdown();
});

function loadStudentsDropdown() {
    fetch('/php/student/read.php')
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('certStudentSelect');
        data.forEach(s => {
            select.innerHTML += `<option value="${s.id}" data-license="${s.license_type}">${s.full_name}</option>`;
        });
    });
}

function loadCerts() {
    fetch('/php/certificate/read.php')
    .then(r => r.json())
    .then(data => {
        const tbody = document.getElementById('certsBody');
        if (!data.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No certificates issued yet.</td></tr>';
            return;
        }
        tbody.innerHTML = data.map((c, i) => `
            <tr>
                <td>${i+1}</td>
                <td>${c.student_name}</td>
                <td><span class="badge bg-secondary">${c.license_type}</span></td>
                <td>${c.issue_date}</td>
                <td><code>${c.cert_number}</code></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="previewCert(${JSON.stringify(c).replace(/"/g, '&quot;')})">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteCert(${c.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    });
}

function submitIssueCert() {
    const formData = new FormData(document.getElementById('issueCertForm'));
    const alert = document.getElementById('certAlert');

    fetch('/php/certificate/issue.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        alert.className = `alert alert-${data.status === 'success' ? 'success' : 'danger'}`;
        alert.textContent = data.message;
        alert.classList.remove('d-none');
        if (data.status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('issueCertModal')).hide();
            document.getElementById('issueCertForm').reset();
            loadCerts();
        }
    });
}

function previewCert(c) {
    document.getElementById('certPrintArea').innerHTML = `
        <div style="border: 8px double #01696f; padding: 50px;">
            <h1 style="color:#01696f; font-size:2rem; margin-bottom:5px;">DSMS</h1>
            <p style="color:#666; margin-bottom:30px;">Driving School Management System — Nepal</p>
            <h2 style="font-size:1.5rem; margin-bottom:5px;">Certificate of Completion</h2>
            <p style="color:#666;">This is to certify that</p>
            <h3 style="font-size:2rem; color:#01696f; margin: 15px 0;">${c.student_name}</h3>
            <p>has successfully completed the driving training program for</p>
            <h4 style="margin: 10px 0;">License Category: ${c.license_type}</h4>
            <p style="margin-top: 20px;">Issue Date: <strong>${c.issue_date}</strong></p>
            <p>Certificate No: <strong>${c.cert_number}</strong></p>
            <div style="margin-top: 50px; display: flex; justify-content: space-around;">
                <div>
                    <div style="border-top: 1px solid #000; width: 150px; margin: 0 auto;"></div>
                    <p style="margin-top: 5px;">Instructor Signature</p>
                </div>
                <div>
                    <div style="border-top: 1px solid #000; width: 150px; margin: 0 auto;"></div>
                    <p style="margin-top: 5px;">School Director</p>
                </div>
            </div>
        </div>
    `;
    new bootstrap.Modal(document.getElementById('printCertModal')).show();
}

function deleteCert(id) {
    if (!confirm('Delete this certificate?')) return;
    fetch('/php/certificate/delete.php', {
        method: 'POST',
        body: new URLSearchParams({ id })
    })
    .then(r => r.json())
    .then(data => { if (data.status === 'success') loadCerts(); });
}
</script>
</body>
</html>