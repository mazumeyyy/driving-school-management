<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$student_id  = intval($_POST['student_id'] ?? 0);
$issue_date  = trim($_POST['issue_date'] ?? '');
$notes       = trim($_POST['notes'] ?? '');

if (!$student_id || !$issue_date) {
    echo json_encode(['status'=>'error','message'=>'Student and date are required.']);
    exit;
}

// Get license_type from student record
$s = $conn->query("SELECT license_type FROM students WHERE id = $student_id");
$student = $s->fetch_assoc();
$license_type = $student['license_type'] ?? '';

$stmt = $conn->prepare("INSERT INTO certificates (student_id, issued_date, license_type, notes) VALUES (?,?,?,?)");
$stmt->bind_param("isss", $student_id, $issue_date, $license_type, $notes);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Certificate issued successfully!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed: ' . $stmt->error]);
}
$stmt->close(); $conn->close();
?>

