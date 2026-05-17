<?php
require_once 'config.php';

$movie_id = (int) ($_POST['movie_id'] ?? 0);
$user_id = (int) ($_POST['user_id'] ?? 0);
$seats = $_POST['seats'] ?? '';
$total = (float) ($_POST['total'] ?? 0);

if (!$movie_id || !$user_id || empty($seats)) {
    echo "<script>alert('Missing data'); window.history.back();</script>";
    exit();
}

$newSeats = explode(",", $seats);

/* CHECK SEAT CONFLICTS */
$seatQuery = $conn->query("
    SELECT s.seat_label
    FROM booked_seats bs
    JOIN seats s ON bs.seat_id = s.seat_id
    JOIN bookings b ON bs.booking_id = b.booking_id
    WHERE b.movie_id = $movie_id
");

$bookedSeats = [];
while ($row = $seatQuery->fetch_assoc()) {
    $bookedSeats[] = $row['seat_label'];
}

foreach ($newSeats as $seat) {
    if (in_array(trim($seat), $bookedSeats)) {
        echo "<script>
            alert('Seat already booked!');
            window.location.href='admin_seat.php?movie_id=$movie_id&user_id=$user_id';
        </script>";
        exit();
    }
}

/* SAVE BOOKING (admin booking - marked as paid with no payment details) */
$stmt = $conn->prepare("
    INSERT INTO bookings 
    (user_id, movie_id, total_price, payment_status, payment_method, payment_reference, payment_timestamp, created_at)
    VALUES (?, ?, ?, 'paid', 'admin', ?, NOW(), NOW())
");

$reference = "ADMIN-" . time();
$stmt->bind_param("iids", $user_id, $movie_id, $total, $reference);
$stmt->execute();

$booking_id = $conn->insert_id;

/* SAVE SEATS TO JUNCTION TABLE */
foreach ($newSeats as $seatLabel) {
    $seatQuery = $conn->prepare("SELECT seat_id FROM seats WHERE seat_label = ?");
    $seatQuery->bind_param("s", $seatLabel);
    $seatQuery->execute();
    $seatResult = $seatQuery->get_result();
    $seatRow = $seatResult->fetch_assoc();
    
    if ($seatRow) {
        $seat_id = $seatRow['seat_id'];
        $bookedStmt = $conn->prepare("INSERT INTO booked_seats (booking_id, seat_id) VALUES (?, ?)");
        $bookedStmt->bind_param("ii", $booking_id, $seat_id);
        $bookedStmt->execute();
    }
}

header("Location: admin.php");
exit();
?>
