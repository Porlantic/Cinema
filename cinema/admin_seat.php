<?php
require_once 'config.php';

$movie_id = (int) ($_GET['movie_id'] ?? 0);
$user_id = (int) ($_GET['user_id'] ?? 0);

if (!$movie_id || !$user_id) {
    echo "Missing movie or user. <a href='admin.php'>Go back</a>";
    exit();
}

$movie = $conn->query("SELECT * FROM movies WHERE movie_id = $movie_id")->fetch_assoc();
$user = $conn->query("SELECT * FROM users WHERE user_id = $user_id")->fetch_assoc();

if (!$movie || !$user) {
    echo "Invalid movie or user. <a href='admin.php'>Go back</a>";
    exit();
}

/* GET BOOKED SEATS FOR THIS MOVIE */
$bookedSeats = [];

$seatQuery = $conn->query("
    SELECT s.seat_label
    FROM booked_seats bs
    JOIN seats s ON bs.seat_id = s.seat_id
    JOIN bookings b ON bs.booking_id = b.booking_id
    WHERE b.movie_id = $movie_id
");

if ($seatQuery) {
    while ($row = $seatQuery->fetch_assoc()) {
        $bookedSeats[] = $row['seat_label'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Seat Selection</title>
    <link rel="stylesheet" href="user_seats.css">
</head>

<body>

<div class="movie-booking-header">

    <h2 class="movie-title">
        <?= htmlspecialchars($movie['title']) ?>
    </h2>

    <div class="movie-booking-info">

        <div class="booking-detail">
            <span class="label">Booking For</span>
            <span class="value"><?= htmlspecialchars($user['name']) ?></span>
        </div>

        <div class="booking-detail">
            <span class="label">Price</span>
            <span class="value">₱<?= number_format($movie['price'], 2) ?></span>
        </div>

        <div class="booking-detail">
            <span class="label">Selected Seats</span>
            <span class="value" id="selectedSeatsText">None</span>
        </div>

        <div class="booking-detail">
            <span class="label">Total</span>
            <span class="value">₱<span id="totalPriceText">0.00</span></span>
        </div>

    </div>

</div>

<div class="screen">SCREEN</div>

<div class="seat-legend">
    <div class="legend-item">
        <div class="legend-box available"></div>
        Available
    </div>
    <div class="legend-item">
        <div class="legend-box selected-box"></div>
        Selected
    </div>
    <div class="legend-item">
        <div class="legend-box booked-box"></div>
        Booked
    </div>
</div>

<form method="POST" action="admin_save_booking.php" id="bookingForm">

    <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
    <input type="hidden" name="user_id" value="<?= $user_id ?>">
    <input type="hidden" name="seats" id="seatInput">
    <input type="hidden" name="total" id="totalInput">

    <div class="seat-container">

        <?php
        $rows = ['E','D','C','B','A'];
        
        $seatConfig = [
            'E' => ['left' => 4, 'middle' => 10, 'right' => 4],
            'D' => ['left' => 5, 'middle' => 10, 'right' => 5],
            'C' => ['left' => 5, 'middle' => 10, 'right' => 5],
            'B' => ['left' => 6, 'middle' => 10, 'right' => 6],
            'A' => ['left' => 6, 'middle' => 10, 'right' => 6]
        ];

        foreach ($rows as $row) {
            $config = $seatConfig[$row];
            
            echo "<div class='seat-row row-$row'>";
            echo "<div class='row-label'>$row</div>";

            $seatNumber = 1;

            // Left section
            for ($i = 1; $i <= $config['left']; $i++) {
                $seatID = $row . '-' . $seatNumber;
                $isBooked = in_array($seatID, $bookedSeats);
                $class = $isBooked ? "seat booked" : "seat";
                $disabled = $isBooked ? "disabled" : "";

                echo "<button type='button' class='$class' data-seat='$seatID' $disabled>$seatNumber</button>";
                $seatNumber++;
            }

            echo "<div class='aisle'></div>";

            // Middle section
            for ($i = 1; $i <= $config['middle']; $i++) {
                $seatID = $row . '-' . $seatNumber;
                $isBooked = in_array($seatID, $bookedSeats);
                $class = $isBooked ? "seat booked" : "seat";
                $disabled = $isBooked ? "disabled" : "";

                echo "<button type='button' class='$class' data-seat='$seatID' $disabled>$seatNumber</button>";
                $seatNumber++;
            }

            echo "<div class='aisle'></div>";

            // Right section
            for ($i = 1; $i <= $config['right']; $i++) {
                $seatID = $row . '-' . $seatNumber;
                $isBooked = in_array($seatID, $bookedSeats);
                $class = $isBooked ? "seat booked" : "seat";
                $disabled = $isBooked ? "disabled" : "";

                echo "<button type='button' class='$class' data-seat='$seatID' $disabled>$seatNumber</button>";
                $seatNumber++;
            }

            echo "</div>";
        }
        ?>
    </div>

    <div class="action-buttons">
        <a href="admin.php" class="back-btn">← Back</a>
        <button type="submit" class="confirm" disabled>Add Booking</button>
    </div>

</form>

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
        updateSummary();
    });
});

function updateSummary() {
    document.getElementById("selectedSeatsText").innerText =
        selectedSeats.length > 0 ? selectedSeats.join(", ") : "None";

    let total = selectedSeats.length * <?= $movie['price'] ?>;
    document.getElementById("totalPriceText").innerText = total.toFixed(2);
    document.querySelector(".confirm").disabled = selectedSeats.length === 0;
}

document.getElementById("bookingForm").addEventListener("submit", function(e) {
    if (selectedSeats.length === 0) {
        e.preventDefault();
        alert("Please select at least one seat");
        return;
    }
    document.getElementById("seatInput").value = selectedSeats.join(",");
    document.getElementById("totalInput").value = selectedSeats.length * <?= $movie['price'] ?>;
});
</script>

</body>
</html>
