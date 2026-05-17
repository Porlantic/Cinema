<?php
require_once 'config.php';
?>

<link rel="stylesheet" href="user_movies.css">

<!-- ================= NOW SHOWING ================= -->
<h2 class="section-title">Now Showing</h2>

<div class="movie-grid">

<?php
$now = $conn->query("
    SELECT m.*, GROUP_CONCAT(g.name SEPARATOR ', ') AS genres
    FROM movies m
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.genre_id
    WHERE m.status='now-showing' 
    GROUP BY m.movie_id
    ORDER BY m.movie_id DESC
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
        <?php if (!empty($movie['genres'])): ?>
        <p class="genre"><?= htmlspecialchars($movie['genres']) ?></p>
        <?php endif; ?>
        <p><?= htmlspecialchars($movie['description']) ?></p>
        <p class="price">₱<?= number_format($movie['price'], 2) ?></p>

        <button class="open-modal"
            data-id="<?= $movie['movie_id'] ?>"
            data-title="<?= htmlspecialchars($movie['title']) ?>"
            data-genre="<?= htmlspecialchars($movie['genres'] ?? '') ?>"
            data-desc="<?= htmlspecialchars($movie['description']) ?>"
            data-price="<?= $movie['price'] ?>"
            data-poster="<?= $poster ?>"
            data-date="<?= !empty($movie['show_date']) ? date('M d, Y', strtotime($movie['show_date'])) . ' • ' . ($movie['show_time'] ?? '') : '' ?>"
        >
            Book Now
        </button>

    </div>

</div>

<?php } ?>

</div>

<!-- ================= SCHEDULED ================= -->
<h2 class="section-title">Coming Soon</h2>

<div class="scheduled-grid">

<?php
$scheduled = $conn->query("
    SELECT * FROM movies 
    WHERE status='scheduled' 
    ORDER BY show_date ASC
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
            <?= !empty($movie['show_date'])
                ? date('M d, Y', strtotime($movie['show_date'])) . ' • ' . ($movie['show_time'] ?? 'No time')
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

        <p id="modalGenre" class="genre"></p>

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
        document.getElementById("modalGenre").innerText = btn.dataset.genre || '';
        document.getElementById("modalDesc").innerText = btn.dataset.desc;
        document.getElementById("modalPrice").innerText = "₱" + btn.dataset.price;

        document.getElementById("modalDate").innerText =
            btn.dataset.date ? btn.dataset.date : "No schedule";

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