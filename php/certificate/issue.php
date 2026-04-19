<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$student_id = intval($_POST['student_id'] ?? 0);
$issue_date = trim($_POST['issue_date'] ?? '');
$notes      = trim($_POST['notes'] ?? '');

if (!$student_id || !$issue_date) {
    echo json_encode(['status'=>'error','message'=>'Student and date are required.']);
    exit;
}

// Generate unique certificate number
$cert_number = 'DSMS-' . strtoupper(substr(md5(uniqid()), 0, 8));

$stmt = $conn->prepare("INSERT INTO certificates (student_id, issue_date, cert_number, notes) VALUES (?,?,?,?)");
$stmt->bind_param("isss", $student_id, $issue_date, $cert_number, $notes);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Certificate issued successfully!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to issue certificate.']);
}
$stmt->close(); $conn->close();
?>