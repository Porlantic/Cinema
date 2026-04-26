<?php require_once 'config.php'; ?>

<div class="section-header">
    
</div>

<div class="booking-table">

<table>

<thead>
<tr>
    <th>Booking ID</th>
    <th>Movie ID</th>
    <th>Movie Title</th>
    <th>Name</th>
    <th>Seats</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
</tr>
</thead>

<tbody>

<?php
$sql = "
SELECT 
    b.booking_id,
    b.movie_id,
    b.customer_name,
    b.seats,
    b.total_price,
    b.payment_status,
    b.created_at,
    m.title AS movie_title
FROM bookings b
LEFT JOIN movies m ON b.movie_id = m.movie_id
ORDER BY b.booking_id DESC
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<tr>
        <td colspan='8' style='text-align:center;padding:20px;'>
        No bookings yet
        </td>
    </tr>";
} else {

    while ($row = $result->fetch_assoc()) {
?>

<tr>
    <td><?= $row['booking_id'] ?></td>
    <td><?= $row['movie_id'] ?></td>
    <td><?= htmlspecialchars($row['movie_title']) ?></td>
    <td><?= htmlspecialchars($row['customer_name']) ?></td>
    <td><?= htmlspecialchars($row['seats']) ?></td>
    <td>₱<?= number_format($row['total_price'], 2) ?></td>
    <td><?= strtoupper($row['payment_status']) ?></td>
    <td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
</tr>

<?php }} ?>

</tbody>

</table>

</div>