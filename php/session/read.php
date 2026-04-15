<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$result = $conn->query("
    SELECT s.*, st.full_name AS student_name
    FROM sessions s
    JOIN students st ON s.student_id = st.id
    ORDER BY s.session_date DESC, s.start_time ASC
");

$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row;
}
echo json_encode($sessions);
$conn->close();
?>

