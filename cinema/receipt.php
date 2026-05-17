<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = (int) ($_GET['booking_id'] ?? 0);
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
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo "Booking not found";
    exit();
}

$seatList = explode(', ', $data['seats']);
$schedule = !empty($data['show_date']) 
    ? date('M d, Y', strtotime($data['show_date'])) . ' • ' . ($data['show_time'] ?? '') 
    : 'N/A';

$paymentDisplay = '';
if ($data['payment_method'] == 'card') {
    $paymentDisplay = $data['card_type'] . ' ****' . $data['card_last4'];
} elseif ($data['payment_method'] == 'gcash') {
    $paymentDisplay = 'GCash ***' . $data['gcash_last3'];
} else {
    $paymentDisplay = ucfirst($data['payment_method'] ?? 'N/A');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt - Booking #<?= $data['booking_id'] ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Inter', sans-serif;
    background: #0a0a0a;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    padding: 40px 20px;
}

.receipt-wrapper {
    width: 100%;
    max-width: 440px;
}

/* ===== RECEIPT CARD ===== */
.receipt {
    background: #141414;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    overflow: hidden;
}

.receipt-header {
    background: #e50914;
    padding: 24px 28px;
    text-align: center;
}

.receipt-header .logo {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.receipt-header .subtitle {
    font-size: 11px;
    font-weight: 400;
    opacity: 0.85;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.receipt-body {
    padding: 28px;
}

.receipt-success {
    text-align: center;
    margin-bottom: 24px;
}

.receipt-success .check {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: rgba(76,175,80,0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

.receipt-success .check svg {
    width: 24px;
    height: 24px;
    fill: #4caf50;
}

.receipt-success h2 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 2px;
}

.receipt-success p {
    font-size: 12px;
    color: rgba(255,255,255,0.4);
}

/* MOVIE SECTION */
.receipt-movie {
    text-align: center;
    padding-bottom: 20px;
    margin-bottom: 20px;
    border-bottom: 1px dashed rgba(255,255,255,0.1);
}

.receipt-movie h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 6px;
}

.receipt-movie .schedule {
    font-size: 13px;
    color: #e50914;
    font-weight: 500;
}

/* INFO ROWS */
.receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}

.receipt-row:last-child {
    border-bottom: none;
}

.receipt-row .label {
    font-size: 11px;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 500;
}

.receipt-row .val {
    font-size: 13px;
    font-weight: 600;
    color: #fff;
    text-align: right;
    max-width: 60%;
}

/* SEATS BREAKDOWN */
.seats-breakdown {
    margin: 16px 0;
    padding: 16px;
    background: rgba(255,255,255,0.03);
    border-radius: 8px;
}

.seats-breakdown .sb-title {
    font-size: 10px;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 10px;
    font-weight: 600;
}

.seat-line {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    font-size: 12px;
    color: rgba(255,255,255,0.7);
}

.seat-line .seat-name {
    font-weight: 500;
}

.seat-line .seat-cost {
    color: rgba(255,255,255,0.5);
}

/* TOTAL */
.receipt-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0 0;
    margin-top: 8px;
    border-top: 1px dashed rgba(255,255,255,0.1);
}

.receipt-total .label {
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,0.6);
}

.receipt-total .amount {
    font-size: 22px;
    font-weight: 800;
    color: #e50914;
}

/* REFERENCE */
.receipt-ref {
    text-align: center;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid rgba(255,255,255,0.06);
}

.receipt-ref .ref-label {
    font-size: 9px;
    color: rgba(255,255,255,0.3);
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 4px;
}

.receipt-ref .ref-number {
    font-size: 11px;
    color: rgba(255,255,255,0.5);
    font-family: monospace;
    letter-spacing: 0.5px;
}

/* ACTIONS */
.receipt-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.receipt-actions button {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 6px;
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-download {
    background: #e50914;
    color: #fff;
}

.btn-download:hover {
    background: #f40612;
    box-shadow: 0 4px 15px rgba(229,9,20,0.4);
}

.btn-back {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.1) !important;
}

.btn-back:hover {
    background: rgba(255,255,255,0.12);
    color: #fff;
}

/* PRINT STYLES */
@media print {
    body { background: #fff; padding: 0; }
    .receipt-wrapper { max-width: 100%; }
    .receipt { border: 2px solid #000; }
    .receipt-header { background: #e50914; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .receipt-body { color: #000; }
    .receipt-row .label { color: #666; }
    .receipt-row .val { color: #000; }
    .receipt-movie h3 { color: #000; }
    .receipt-movie .schedule { color: #e50914; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .receipt-total .label { color: #333; }
    .receipt-total .amount { color: #e50914; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .receipt-success .check { background: rgba(76,175,80,0.15); -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .receipt-success h2 { color: #000; }
    .receipt-success p { color: #666; }
    .seats-breakdown { background: #f5f5f5; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .seat-line { color: #333; }
    .receipt-ref .ref-label { color: #999; }
    .receipt-ref .ref-number { color: #333; }
    .receipt-actions { display: none !important; }
}
</style>
</head>
<body>

<div class="receipt-wrapper">
    <div class="receipt" id="receiptCard">

        <div class="receipt-header">
            <div class="logo">Cinema</div>
            <div class="subtitle">Payment Receipt</div>
        </div>

        <div class="receipt-body">

            <div class="receipt-success">
                <div class="check">
                    <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                </div>
                <h2>Payment Successful</h2>
                <p>Booking #<?= $data['booking_id'] ?></p>
            </div>

            <div class="receipt-movie">
                <h3><?= htmlspecialchars($data['title']) ?></h3>
                <div class="schedule"><?= $schedule ?></div>
            </div>

            <div class="receipt-row">
                <span class="label">Customer</span>
                <span class="val"><?= htmlspecialchars($data['user_name']) ?></span>
            </div>

            <div class="receipt-row">
                <span class="label">Email</span>
                <span class="val"><?= htmlspecialchars($data['user_email']) ?></span>
            </div>

            <div class="receipt-row">
                <span class="label">Seats</span>
                <span class="val"><?= htmlspecialchars($data['seats']) ?></span>
            </div>

            <div class="receipt-row">
                <span class="label">Payment</span>
                <span class="val"><?= $paymentDisplay ?></span>
            </div>

            <div class="receipt-row">
                <span class="label">Date</span>
                <span class="val"><?= date('M d, Y h:i A', strtotime($data['created_at'])) ?></span>
            </div>

            <div class="seats-breakdown">
                <div class="sb-title">Seat Breakdown</div>
                <?php foreach ($seatList as $seat): ?>
                <div class="seat-line">
                    <span class="seat-name">Seat <?= htmlspecialchars(trim($seat)) ?></span>
                    <span class="seat-cost">&#8369;<?= number_format($data['seat_price'], 2) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="receipt-total">
                <span class="label">Total Paid</span>
                <span class="amount">&#8369;<?= number_format($data['total_price'], 2) ?></span>
            </div>

            <div class="receipt-ref">
                <div class="ref-label">Reference Number</div>
                <div class="ref-number"><?= htmlspecialchars($data['payment_reference']) ?></div>
            </div>
        </div>
    </div>

    <div class="receipt-actions">
        <button class="btn-download" onclick="downloadReceipt()">Download Receipt</button>
        <button class="btn-back" onclick="window.location.href='user_dashboard.php?tab=bookings'">Go to Dashboard</button>
    </div>
</div>

<script>
function downloadReceipt() {
    window.print();
}
</script>

</body>
</html>
