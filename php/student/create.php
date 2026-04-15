<?php
session_start();
if (!isset($_SESSION['user_id'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }
require_once '../config/db.php';
header('Content-Type: application/json');

$full_name    = trim($_POST['full_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$phone        = trim($_POST['phone'] ?? '');
$address      = trim($_POST['address'] ?? '');
$dob          = $_POST['dob'] ?? null;
$license_type = $_POST['license_type'] ?? 'B';

if (!$full_name) { echo json_encode(['status'=>'error','message'=>'Full name is required.']); exit; }

$stmt = $conn->prepare("INSERT INTO students (full_name, email, phone, address, dob, license_type) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss", $full_name, $email, $phone, $address, $dob, $license_type);

if ($stmt->execute()) {
    echo json_encode(['status'=>'success','message'=>'Student added successfully!']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to add student.']);
}
$stmt->close(); $conn->close();
?>