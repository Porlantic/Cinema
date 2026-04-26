<?php require_once 'config.php'; ?>

<h2 class="section-title">My Bookings</h2>

<div class="booking-list">

<?php
$user_id = $_SESSION['user_id'] ?? 0;

$sql = "
SELECT 
    b.booking_id,
    b.seats,
    b.total_price,
    b.payment_status,
    b.created_at,
    m.title,
    m.poster
FROM bookings b
LEFT JOIN movies m ON b.movie_id = m.movie_id
WHERE b.user_id = $user_id
ORDER BY b.booking_id DESC
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<p style='color:#aaa;'>No bookings yet.</p>";
} else {

while ($row = $result->fetch_assoc()) {

    $poster = !empty($row['poster'])
        ? $row['poster']
        : 'https://via.placeholder.com/150x220/800020/ffffff?text=Movie';
?>

<div class="booking-card">

    <img src="<?= $poster ?>" class="booking-poster">

    <div class="booking-info">

        <h3><?= htmlspecialchars($row['title']) ?></h3>

        <p>Seats: <?= htmlspecialchars($row['seats']) ?></p>

        <p>Total: ₱<?= number_format($row['total_price'], 2) ?></p>

        <p class="date">
            <?= date('M d, Y • h:i A', strtotime($row['created_at'])) ?>
        </p>

        <span class="status <?= strtoupper($row['payment_status']) ?>">
            <?= strtoupper($row['payment_status']) ?>
        </span>

    </div>

</div>

<?php }} ?>

</div>