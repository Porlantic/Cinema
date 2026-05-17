<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$movie_id = (int) ($_POST['movie_id'] ?? 0);
$seats = $_POST['seats'] ?? '';
$total = (float) ($_POST['total'] ?? 0);


if (empty($seats)) {
    echo "<script>
        alert('No seats selected');
        window.history.back();
    </script>";
    exit();
}

$newSeats = explode(",", $seats);

/* GET EXISTING BOOKED SEATS FROM JUNCTION TABLE */
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

/* CHECK FOR CONFLICT */
foreach ($newSeats as $seat) {
    if (in_array(trim($seat), $bookedSeats)) {
        echo "<script>
            alert('Seat already booked! Please choose another.');
            window.location.href='user_seats.php?movie_id=$movie_id';
        </script>";
        exit();
    }
}

/* SAVE BOOKING */
$user_id = $_SESSION['user_id'];

// Handle payment data
$paymentData = isset($_POST['payment_data']) ? json_decode($_POST['payment_data'], true) : null;

$paymentMethod = $paymentData['method'] ?? null;
$paymentReference = $paymentData['reference'] ?? null;
$cardLast4 = $paymentData['cardLast4'] ?? null;
$cardType = $paymentData['cardType'] ?? null;
$gcashLast3 = $paymentData['gcashLast3'] ?? null;

$stmt = $conn->prepare("
    INSERT INTO bookings 
    (user_id, movie_id, total_price, payment_status, payment_method, payment_reference, payment_timestamp, card_last4, card_type, gcash_last3, created_at)
    VALUES (?, ?, ?, 'paid', ?, ?, NOW(), ?, ?, ?, NOW())
");

$stmt->bind_param("iidsssss", $user_id, $movie_id, $total, $paymentMethod, $paymentReference, $cardLast4, $cardType, $gcashLast3);
$stmt->execute();

$booking_id = $conn->insert_id;

/* SAVE SEATS TO JUNCTION TABLE */
foreach ($newSeats as $seatLabel) {
    // Get seat_id from seats table
    $seatQuery = $conn->prepare("SELECT seat_id FROM seats WHERE seat_label = ?");
    $seatQuery->bind_param("s", $seatLabel);
    $seatQuery->execute();
    $seatResult = $seatQuery->get_result();
    $seatRow = $seatResult->fetch_assoc();
    
    if ($seatRow) {
        $seat_id = $seatRow['seat_id'];
        
        // Insert into booked_seats junction table
        $bookedStmt = $conn->prepare("INSERT INTO booked_seats (booking_id, seat_id) VALUES (?, ?)");
        $bookedStmt->bind_param("ii", $booking_id, $seat_id);
        $bookedStmt->execute();
    }
}

header("Location: receipt.php?booking_id=" . $booking_id);
exit();
?>