<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

$booking_id = (int) ($_GET['id'] ?? 0);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        b.total_price,
        b.payment_status,
        b.created_at,
        b.payment_method,
        b.payment_reference,
        b.card_last4,
        b.card_type,
        b.gcash_last3,
        m.title,
        m.price AS seat_price,
        m.show_date,
        m.show_time,
        u.name AS user_name,
        u.email AS user_email,
        GROUP_CONCAT(s.seat_label ORDER BY s.seat_label SEPARATOR ', ') AS seats,
        COUNT(s.seat_id) AS seat_count
    FROM bookings b
    LEFT JOIN movies m ON b.movie_id = m.movie_id
    LEFT JOIN users u ON b.user_id = u.user_id
    LEFT JOIN booked_seats bs ON b.booking_id = bs.booking_id
    LEFT JOIN seats s ON bs.seat_id = s.seat_id
    WHERE b.booking_id = ? AND b.user_id = ?
    GROUP BY b.booking_id
");

$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    echo json_encode(['error' => 'Booking not found']);
    exit();
}

echo json_encode(['success' => true, 'data' => $result]);
exit;
