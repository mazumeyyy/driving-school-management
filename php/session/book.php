<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$student_id   = intval($_POST['student_id'] ?? 0);
$session_date = trim($_POST['session_date'] ?? '');
$start_time   = trim($_POST['start_time'] ?? '');
$end_time     = trim($_POST['end_time'] ?? '');
$status       = trim($_POST['status'] ?? 'scheduled');
$notes        = trim($_POST['notes'] ?? '');

if (!$student_id || !$session_date) {
    echo json_encode(['status'=>'error','message'=>'Student and date are required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO sessions (student_id, session_date, start_time, end_time, status, notes) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("isssss", $student_id, $session_date, $start_time, $end_time, $status, $notes);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Session booked successfully!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to book session.']);
}
$stmt->close(); $conn->close();
?>

