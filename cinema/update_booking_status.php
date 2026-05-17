<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['booking_id']) || !isset($data['status'])) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

$id = $data['booking_id'];
$status = $data['status'];

$stmt = $conn->prepare("UPDATE bookings SET payment_status=? WHERE booking_id=?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "DB update failed"]);
}
?>