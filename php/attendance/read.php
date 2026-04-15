<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode([]); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$result = $conn->query("
    SELECT a.*, st.full_name AS student_name, se.session_date
    FROM attendance a
    JOIN students st ON a.student_id = st.id
    JOIN sessions se ON a.session_id = se.id
    ORDER BY a.marked_at DESC
");

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}
echo json_encode($records);
$conn->close();
?>