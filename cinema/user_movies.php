<?php
require_once 'config.php';
?>

<link rel="stylesheet" href="user_movies.css">

<!-- ================= NOW SHOWING ================= -->
<h2 class="section-title">Now Showing</h2>

<div class="movie-grid">

<?php
$now = $conn->query("
    SELECT * FROM movies 
    WHERE status='now-showing' 
    ORDER BY movie_id DESC
");

while ($movie = $now->fetch_assoc()) {

    $poster = !empty($movie['poster'])
        ? $movie['poster']
        : 'https://via.placeholder.com/300x450/800020/ffffff?text=' . urlencode($movie['title']);
?>

<div class="movie-card">

    <img src="<?= $poster ?>">

    <div class="info">
        <h3><?= htmlspecialchars($movie['title']) ?></h3>
        <p><?= htmlspecialchars($movie['description']) ?></p>
        <p class="price">₱<?= number_format($movie['price'], 2) ?></p>

        <button class="open-modal"
            data-id="<?= $movie['movie_id'] ?>"
            data-title="<?= htmlspecialchars($movie['title']) ?>"
            data-desc="<?= htmlspecialchars($movie['description']) ?>"
            data-price="<?= $movie['price'] ?>"
            data-poster="<?= $poster ?>"
            data-date="<?= $movie['datetime'] ?>"
        >
            Book Now
        </button>

    </div>

</div>

<?php } ?>

</div>

<!-- ================= SCHEDULED ================= -->
<h2 class="section-title">Scheduled</h2>

<div class="scheduled-grid">

<?php
$scheduled = $conn->query("
    SELECT * FROM movies 
    WHERE status='scheduled' 
    ORDER BY datetime ASC
");

while ($movie = $scheduled->fetch_assoc()) {

    $poster = !empty($movie['poster'])
        ? $movie['poster']
        : 'https://via.placeholder.com/200x300/800020/ffffff?text=' . urlencode($movie['title']);
?>

<div class="scheduled-card">

    <img src="<?= $poster ?>">

    <div class="info">
        <h4><?= htmlspecialchars($movie['title']) ?></h4>

        <small>
            <?= !empty($movie['datetime'])
                ? date('M d, Y • h:i A', strtotime($movie['datetime']))
                : 'No schedule' ?>
        </small>

        <p class="price">₱<?= number_format($movie['price'], 2) ?></p>
    </div>

</div>

<?php } ?>

</div>

<!-- ================= MODAL ================= -->
<div id="movieModal" class="modal">

    <div class="modal-content">

        <span class="close">&times;</span>

        <img id="modalPoster" />

        <h2 id="modalTitle"></h2>

        <p id="modalDesc"></p>

        <p class="price" id="modalPrice"></p>

        <p id="modalDate"></p>

        <a id="seatLink" href="#">
            <button>
                Select Seats
            </button>
        </a>

    </div>

</div>

<!-- ================= MODAL SCRIPT ================= -->
<script>
const modal = document.getElementById("movieModal");
const closeBtn = document.querySelector(".close");

document.querySelectorAll(".open-modal").forEach(btn => {
    btn.addEventListener("click", () => {

        document.getElementById("modalPoster").src = btn.dataset.poster;
        document.getElementById("modalTitle").innerText = btn.dataset.title;
        document.getElementById("modalDesc").innerText = btn.dataset.desc;
        document.getElementById("modalPrice").innerText = "₱" + btn.dataset.price;

        document.getElementById("modalDate").innerText =
            btn.dataset.date ? btn.dataset.date : "No schedule";

        modal.style.display = "flex";
    });
});
document.querySelectorAll(".open-modal").forEach(btn => {
    btn.addEventListener("click", () => {

        document.getElementById("modalPoster").src = btn.dataset.poster;
        document.getElementById("modalTitle").innerText = btn.dataset.title;
        document.getElementById("modalDesc").innerText = btn.dataset.desc;
        document.getElementById("modalPrice").innerText = "₱" + btn.dataset.price;

        document.getElementById("modalDate").innerText =
            btn.dataset.date ? btn.dataset.date : "No schedule";

        // 🔥 ADD THIS (REDIRECT LINK)
        document.getElementById("seatLink").href =
            "user_seats.php?movie_id=" + btn.dataset.id;

        modal.style.display = "flex";
    });
});

closeBtn.onclick = () => {
    modal.style.display = "none";
};

window.onclick = (e) => {
    if (e.target == modal) {
        modal.style.display = "none";
    }
};
</script>