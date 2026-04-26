<?php
require_once 'config.php';
session_start();

$movie_id = (int) $_POST['movie_id'];
$seats = $_POST['seats'] ?? '';
$total = $_POST['total'] ?? 0;

$user_name = $_SESSION['user_name'] ?? 'Guest';

if (empty($seats)) {
    echo "<script>
        alert('No seats selected');
        window.history.back();
    </script>";
    exit();
}

$newSeats = explode(",", $seats);

/* GET EXISTING BOOKED SEATS */
$seatQuery = $conn->query("
    SELECT seats 
    FROM bookings 
    WHERE movie_id = $movie_id
");

$bookedSeats = [];

while ($row = $seatQuery->fetch_assoc()) {
    $existing = explode(",", $row['seats']);

    foreach ($existing as $s) {
        $bookedSeats[] = trim($s);
    }
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
$conn->query("
    INSERT INTO bookings 
    (movie_id, customer_name, seats, total_price, payment_status, created_at)
    VALUES
    ($movie_id, '$user_name', '$seats', $total, 'pending', NOW())
");

header("Location: user_movies.php");
exit();
?>