<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM students ORDER BY enrolled_at DESC");
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
echo json_encode($students);
$conn->close();
?>