<?php
require_once 'config.php';

$user_id = $_SESSION['user_id'];

if (!$user_id) {
    echo "<p style='color:#aaa;'>Please log in.</p>";
    exit();
}

$sql = "
SELECT 
    b.booking_id,
    b.total_price,
    b.payment_status,
    b.created_at,
    b.payment_method,
    b.payment_reference,
    b.payment_timestamp,
    b.card_last4,
    b.card_type,
    b.gcash_last3,
    m.title,
    m.poster,
    m.show_date,
    m.show_time,
    m.price AS seat_price,
    GROUP_CONCAT(s.seat_label ORDER BY s.seat_label SEPARATOR ', ') as seats
FROM bookings b
LEFT JOIN movies m ON b.movie_id = m.movie_id
LEFT JOIN booked_seats bs ON b.booking_id = bs.booking_id
LEFT JOIN seats s ON bs.seat_id = s.seat_id
WHERE b.user_id = $user_id
GROUP BY b.booking_id
ORDER BY b.created_at DESC
";

$result = $conn->query($sql);
?>

<link rel="stylesheet" href="user_booking.css">

<div class="booking-list">

<?php
if (!$result || $result->num_rows === 0) {
    echo "<p class='no-bookings'>No bookings yet.</p>";
} else {
?>

<div class="table-wrapper">
<table class="booking-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Movie</th>
            <th>Schedule</th>
            <th>Seats</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Booked On</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td class="td-id"><?= $row['booking_id'] ?></td>
            <td class="td-movie"><?= htmlspecialchars($row['title']) ?></td>
            <td class="td-schedule">
                <?php if (!empty($row['show_date'])): ?>
                    <?= date('M d, Y', strtotime($row['show_date'])) ?><br>
                    <span class="schedule-time"><?= $row['show_time'] ?? '' ?></span>
                <?php else: ?>
                    <span class="text-muted">N/A</span>
                <?php endif; ?>
            </td>
            <td class="td-seats"><?= !empty($row['seats']) ? htmlspecialchars($row['seats']) : '<span class="text-muted">—</span>' ?></td>
            <td class="td-price">&#8369;<?= number_format($row['total_price'], 2) ?></td>
            <td class="td-payment">
                <?php if ($row['payment_method'] == 'card'): ?>
                    <span class="payment-badge card"><?= $row['card_type'] ?> ****<?= $row['card_last4'] ?></span>
                <?php elseif ($row['payment_method'] == 'gcash'): ?>
                    <span class="payment-badge gcash">GCash ***<?= $row['gcash_last3'] ?></span>
                <?php else: ?>
                    <span class="payment-badge"><?= ucfirst($row['payment_method'] ?? 'N/A') ?></span>
                <?php endif; ?>
            </td>
            <td>
                <span class="status <?= strtolower($row['payment_status']) ?>">
                    <?= ucfirst($row['payment_status']) ?>
                </span>
            </td>
            <td class="td-date"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
            <td class="td-action">
                <?php if (!empty($row['seats']) && strtolower($row['payment_status']) === 'paid'): ?>
                <button class="ticket-btn" onclick='openTicketModal(<?= json_encode([
                    "booking_id" => $row["booking_id"],
                    "title" => $row["title"],
                    "seats" => $row["seats"],
                    "seat_price" => $row["seat_price"],
                    "show_date" => $row["show_date"],
                    "show_time" => $row["show_time"],
                    "created_at" => $row["created_at"]
                ]) ?>)'>Ticket</button>
                <button class="refund-btn" onclick="openRefundModal(<?= $row['booking_id'] ?>, '<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['seats'], ENT_QUOTES) ?>', <?= $row['total_price'] ?>)">Refund</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>

<?php } ?>

</div>