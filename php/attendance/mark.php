<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$session_id = intval($_POST['session_id'] ?? 0);
$student_id = intval($_POST['student_id'] ?? 0);
$status     = trim($_POST['status'] ?? 'present');

if (!$session_id || !$student_id) {
    echo json_encode(['status'=>'error','message'=>'Session and student are required.']);
    exit;
}

// Check if already marked
$check = $conn->prepare("SELECT id FROM attendance WHERE session_id = ? AND student_id = ?");
$check->bind_param("ii", $session_id, $student_id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo json_encode(['status'=>'error','message'=>'Attendance already marked for this student in this session.']);
    $check->close(); $conn->close(); exit;
}
$check->close();

$stmt = $conn->prepare("INSERT INTO attendance (session_id, student_id, status) VALUES (?,?,?)");
$stmt->bind_param("iis", $session_id, $student_id, $status);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Attendance marked successfully!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to mark attendance.']);
}
$stmt->close(); $conn->close();
?>