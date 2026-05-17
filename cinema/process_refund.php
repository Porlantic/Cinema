<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$booking_id = (int) ($_POST['booking_id'] ?? 0);
$reference = trim($_POST['reference'] ?? '');
$user_id = $_SESSION['user_id'];

if (!$booking_id || !$reference) {
    echo json_encode(['error' => 'Missing booking ID or reference number']);
    exit();
}

// Verify booking belongs to user and check reference number
$stmt = $conn->prepare("
    SELECT booking_id, payment_reference, payment_status 
    FROM bookings 
    WHERE booking_id = ? AND user_id = ?
");
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    echo json_encode(['error' => 'Booking not found']);
    exit();
}

if ($booking['payment_status'] === 'refunded') {
    echo json_encode(['error' => 'This booking has already been refunded']);
    exit();
}

// Verify reference number matches
if ($booking['payment_reference'] !== $reference) {
    echo json_encode(['error' => 'Invalid reference number. Please check your receipt.']);
    exit();
}

// Update booking status to refunded
$update = $conn->prepare("UPDATE bookings SET payment_status = 'refunded' WHERE booking_id = ?");
$update->bind_param("i", $booking_id);
$update->execute();

// Delete booked seats to free them up
$delSeats = $conn->prepare("DELETE FROM booked_seats WHERE booking_id = ?");
$delSeats->bind_param("i", $booking_id);
$delSeats->execute();

echo json_encode(['success' => true, 'message' => 'Refund processed successfully. Seats have been released.']);
exit;
