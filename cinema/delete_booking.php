<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['booking_id'])) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    exit;
}

$id = (int) $data['booking_id'];

// Delete from junction table first (foreign key constraint)
$stmt1 = $conn->prepare("DELETE FROM booked_seats WHERE booking_id = ?");
$stmt1->bind_param("i", $id);
$stmt1->execute();

// Delete the booking
$stmt2 = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
$stmt2->bind_param("i", $id);

if ($stmt2->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "DB delete failed"]);
}
?>
