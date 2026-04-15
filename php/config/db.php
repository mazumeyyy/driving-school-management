<?php
$host = 'localhost';
$user = 'root';
$pass = 'root';
$db   = 'dsms';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed.']));
}
?>