<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$result = $conn->query("
    SELECT c.*, s.full_name AS student_name, s.license_type
    FROM certificates c
    JOIN students s ON c.student_id = s.id
    ORDER BY c.created_at DESC
");

if (!$result) {
    echo json_encode(['error' => $conn->error]);
    exit;
}

$certs = [];
while ($row = $result->fetch_assoc()) {
    $certs[] = $row;
}
echo json_encode($certs);
$conn->close();
?>