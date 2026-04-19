<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);
if (!$id) { echo json_encode(['status'=>'error','message'=>'Invalid ID.']); exit; }

$stmt = $conn->prepare("DELETE FROM certificates WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Certificate deleted.']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to delete.']);
}
$stmt->close(); $conn->close();
?>

