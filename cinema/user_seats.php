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

/* GET BOOKED SEATS FROM NORMALIZED STRUCTURE */
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
    <title>Seat Selection</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="user_seats.css">
</head>

<body>

<div class="movie-booking-header">

    <h2 class="movie-title">
        <?= htmlspecialchars($movie['title']) ?>
    </h2>

    <div class="movie-booking-info">

        <div class="booking-detail">
            <span class="label">Price</span>
            <span class="value">
                ₱<?= number_format($movie['price'], 2) ?>
            </span>
        </div>

        <div class="booking-detail">
            <span class="label">Selected Seats</span>
            <span class="value" id="selectedSeatsText">
                None
            </span>
        </div>

        <div class="booking-detail">
            <span class="label">Total</span>
            <span class="value">
                ₱<span id="totalPriceText">0.00</span>
            </span>
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
<!-- FORM START -->
<form method="POST" action="save_booking.php" id="bookingForm">

    <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
    <input type="hidden" name="seats" id="seatInput">
    <input type="hidden" name="total" id="totalInput">

    <div class="seat-container">

        <?php
        $rows = ['E','D','C','B','A']; // E is front (narrow), A is back (wide)
        
        // V-shape configuration - wider at back, narrower at front
        $seatConfig = [
            'E' => ['left' => 4, 'middle' => 10, 'right' => 4],  // Front row (narrowest) - reduced sides
            'D' => ['left' => 5, 'middle' => 10, 'right' => 5],
            'C' => ['left' => 5, 'middle' => 10, 'right' => 5],
            'B' => ['left' => 6, 'middle' => 10, 'right' => 6],
            'A' => ['left' => 6, 'middle' => 10, 'right' => 6]   // Back row (widest)
        ];

        foreach ($rows as $row) {
            $config = $seatConfig[$row];
            
            echo "
            <div class='seat-row row-$row'>";

            echo "<div class='row-label'>$row</div>";

            $seatNumber = 1;

            // Left section
            for ($i = 1; $i <= $config['left']; $i++) {
                $seatID = $row . '-' . $seatNumber;
                $isBooked = in_array($seatID, $bookedSeats);
                $class = $isBooked ? "seat booked" : "seat";
                $disabled = $isBooked ? "disabled" : "";

                echo "
                <button
                    type='button'
                    class='$class'
                    data-seat='$seatID'
                    $disabled
                >
                    $seatNumber
                </button>";
                $seatNumber++;
            }

            // Left aisle
            echo "<div class='aisle'></div>";

            // Middle section
            for ($i = 1; $i <= $config['middle']; $i++) {
                $seatID = $row . '-' . $seatNumber;
                $isBooked = in_array($seatID, $bookedSeats);
                $class = $isBooked ? "seat booked" : "seat";
                $disabled = $isBooked ? "disabled" : "";

                echo "
                <button
                    type='button'
                    class='$class'
                    data-seat='$seatID'
                    $disabled
                >
                    $seatNumber
                </button>";
                $seatNumber++;
            }

            // Right aisle
            echo "<div class='aisle'></div>";

            // Right section
            for ($i = 1; $i <= $config['right']; $i++) {
                $seatID = $row . '-' . $seatNumber;
                $isBooked = in_array($seatID, $bookedSeats);
                $class = $isBooked ? "seat booked" : "seat";
                $disabled = $isBooked ? "disabled" : "";

                echo "
                <button
                    type='button'
                    class='$class'
                    data-seat='$seatID'
                    $disabled
                >
                    $seatNumber
                </button>";
                $seatNumber++;
            }

            echo "</div>";
        }
        ?>
    </div>

    <div class="action-buttons">
        <a href="user_dashboard.php" class="back-btn">← Back</a>
        <button type="button" class="confirm" onclick="openPaymentModal()" disabled>
            Confirm Booking
        </button>
    </div>

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
            updateSummary();
        } else {
            seat.classList.add("selected");
            selectedSeats.push(seatID);
            updateSummary();
        }
    });
});
function openPaymentModal() {

    if (selectedSeats.length === 0) return;

    document.getElementById("c_movie").innerText = <?= json_encode($movie['title']) ?>;
    document.getElementById("c_seats").innerText = selectedSeats.join(", ");
    document.getElementById("c_total").innerText =
        (selectedSeats.length * <?= $movie['price'] ?>).toFixed(2);

    document.getElementById("paymentModal").style.display = "block";
}

function closePaymentModal() {
    document.getElementById("paymentModal").style.display = "none";
    // Reset form fields
    document.getElementById("cardNumber").value = "";
    document.getElementById("cardExpiry").value = "";
    document.getElementById("cardCvv").value = "";
    document.getElementById("cardType").value = "";
    document.getElementById("gcashNumber").value = "";
}

function togglePaymentFields() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (paymentMethod) {
        if (paymentMethod.value === 'card') {
            document.getElementById("cardFields").style.display = "block";
            document.getElementById("gcashFields").style.display = "none";
        } else if (paymentMethod.value === 'gcash') {
            document.getElementById("cardFields").style.display = "none";
            document.getElementById("gcashFields").style.display = "block";
        }
    }
}

function processPayment() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (!paymentMethod) {
        alert("Please select a payment method");
        return;
    }

    let paymentData = {
        method: paymentMethod.value
    };

    // Auto-generate reference number
    const referenceNumber = "TXN-" + Date.now() + "-" + Math.random().toString(36).substr(2, 9).toUpperCase();
    paymentData.reference = referenceNumber;

    if (paymentMethod.value === 'card') {
        const cardNumber = document.getElementById("cardNumber").value;
        const cardExpiry = document.getElementById("cardExpiry").value;
        const cardCvv = document.getElementById("cardCvv").value;
        const cardType = document.getElementById("cardType").value;

        if (!cardNumber || !cardExpiry || !cardCvv || !cardType) {
            alert("Please fill in all card details");
            return;
        }

        // Validate card number (basic validation)
        const cleanCardNumber = cardNumber.replace(/\s/g, '');
        if (cleanCardNumber.length < 13 || cleanCardNumber.length > 19 || !/^\d+$/.test(cleanCardNumber)) {
            alert("Please enter a valid card number");
            return;
        }

        // Validate expiry date (MM/YY format)
        if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
            alert("Please enter expiry date in MM/YY format");
            return;
        }

        // Validate CVV
        if (cardCvv.length < 3 || cardCvv.length > 4 || !/^\d+$/.test(cardCvv)) {
            alert("Please enter a valid CVV");
            return;
        }

        // Store only last 4 digits of card
        paymentData.cardLast4 = cleanCardNumber.slice(-4);
        paymentData.cardType = cardType;

    } else if (paymentMethod.value === 'gcash') {
        const gcashNumber = document.getElementById("gcashNumber").value;

        if (!gcashNumber) {
            alert("Please fill in all Gcash details");
            return;
        }

        // Validate Gcash number (basic validation for PH mobile numbers)
        const cleanGcashNumber = gcashNumber.replace(/\s/g, '');
        if (!/^09\d{9}$/.test(cleanGcashNumber)) {
            alert("Please enter a valid Gcash number (09XX XXX XXXX)");
            return;
        }

        // Store only last 3 digits of Gcash number
        paymentData.gcashLast3 = cleanGcashNumber.slice(-3);
    }

    // Submit booking with payment data
    let form = document.getElementById("bookingForm");
    document.getElementById("seatInput").value = selectedSeats.join(",");
    document.getElementById("totalInput").value = selectedSeats.length * <?= $movie['price'] ?>;

    // Add payment data to form
    const paymentInput = document.createElement("input");
    paymentInput.type = "hidden";
    paymentInput.name = "payment_data";
    paymentInput.value = JSON.stringify(paymentData);
    form.appendChild(paymentInput);

    form.submit();
}

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
function updateSummary() {

    document.getElementById("selectedSeatsText").innerText =
        selectedSeats.length > 0
        ? selectedSeats.join(", ")
        : "None";

    let total =
        selectedSeats.length * <?= $movie['price'] ?>;

    document.getElementById("totalPriceText").innerText =
        total.toFixed(2);
    document.querySelector(".confirm").disabled = selectedSeats.length === 0;
}

</script>
<div id="paymentModal" class="modal">
    <div class="modal-content payment-modal">
        <h2>Payment Details</h2>

        <div class="booking-summary">
            <p><strong>Movie:</strong> <span id="c_movie"></span></p>
            <p><strong>Seats:</strong> <span id="c_seats"></span></p>
            <p><strong>Total:</strong> ₱<span id="c_total"></span></p>
        </div>

        <div class="payment-method-section">
            <label class="payment-label">Select Payment Method:</label>
            <div class="payment-options">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="card" onchange="togglePaymentFields()">
                    <span>Card</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="gcash" onchange="togglePaymentFields()">
                    <span>Gcash</span>
                </label>
            </div>
        </div>

        <div id="cardFields" class="payment-fields" style="display: none;">
            <div class="form-group">
                <label>Card Number:</label>
                <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Expiry Date:</label>
                    <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5">
                </div>
                <div class="form-group">
                    <label>CVV:</label>
                    <input type="password" id="cardCvv" placeholder="123" maxlength="4">
                </div>
            </div>
            <div class="form-group">
                <label>Card Type:</label>
                <select id="cardType">
                    <option value="">Select Card Type</option>
                    <option value="Visa">Visa</option>
                    <option value="Mastercard">Mastercard</option>
                    <option value="American Express">American Express</option>
                </select>
            </div>
        </div>

        <div id="gcashFields" class="payment-fields" style="display: none;">
            <div class="form-group">
                <label>Gcash Number:</label>
                <input type="text" id="gcashNumber" placeholder="0912 345 6789" maxlength="13">
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn-pay" onclick="processPayment()">Pay Now</button>
            <button type="button" class="btn-cancel" onclick="closePaymentModal()">Cancel</button>
        </div>
    </div>
</div>
</body>
</html>