<?php
require_once 'config.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
$stmt->bind_param("i", $id);

echo json_encode(["success"=>$stmt->execute()]);
exit;