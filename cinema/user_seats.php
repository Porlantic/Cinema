<?php
require_once 'config.php';

$movie_id = (int) ($_GET['movie_id'] ?? 0);

$movie = $conn->query("
    SELECT * FROM movies WHERE movie_id = $movie_id
")->fetch_assoc();

if (!$movie) {
    echo "Movie not found";
    exit();
}

/* GET BOOKED SEATS */
$bookedSeats = [];

$seatQuery = $conn->query("
    SELECT seats 
    FROM bookings 
    WHERE movie_id = $movie_id
");

if ($seatQuery) {
    while ($row = $seatQuery->fetch_assoc()) {
        $seats = explode(",", $row['seats']);

        foreach ($seats as $seat) {
            $bookedSeats[] = trim($seat);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seat Selection</title>
    <link rel="stylesheet" href="user_seats.css">
</head>

<body>

<h2><?= htmlspecialchars($movie['title']) ?></h2>

<div class="screen">SCREEN</div>

<!-- FORM START -->
<form method="POST" action="save_booking.php" id="bookingForm">

    <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
    <input type="hidden" name="seats" id="seatInput">
    <input type="hidden" name="total" id="totalInput">

    <div class="seat-container">

        <?php
        $rows = ['A','B','C','D','E'];
        $cols = 8;

        foreach ($rows as $row) {
            echo "<div class='seat-row'>";
            echo "<div class='row-label'>$row</div>";

            for ($i = 1; $i <= $cols; $i++) {

                $seatID = $row.$i;
                $isBooked = in_array($seatID, $bookedSeats);

                $class = $isBooked ? "seat booked" : "seat";

                echo "<div class='$class' data-seat='$seatID'></div>";
            }

            echo "</div>";
        }
        ?>

    </div>

    <button class="confirm" type="submit">Confirm Booking</button>

</form>
<!-- FORM END -->

<script>
let seats = document.querySelectorAll(".seat:not(.booked)");
let selectedSeats = [];

seats.forEach(seat => {
    seat.addEventListener("click", () => {

        let seatID = seat.dataset.seat;

        if (seat.classList.contains("selected")) {
            seat.classList.remove("selected");
            selectedSeats = selectedSeats.filter(s => s !== seatID);
        } else {
            seat.classList.add("selected");
            selectedSeats.push(seatID);
        }
    });
});

/* SUBMIT HANDLER */
document.getElementById("bookingForm").addEventListener("submit", function(e) {

    if (selectedSeats.length === 0) {
        e.preventDefault();
        alert("Please select at least one seat");
        return;
    }

    document.getElementById("seatInput").value = selectedSeats.join(",");

    let pricePerSeat = <?= $movie['price'] ?>;
    document.getElementById("totalInput").value = selectedSeats.length * pricePerSeat;
});
</script>

</body>
</html>