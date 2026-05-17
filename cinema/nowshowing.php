<?php
require_once 'config.php';

$sql = "
SELECT m.*, 
       GROUP_CONCAT(g.name SEPARATOR ', ') AS genres
FROM movies m
LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
LEFT JOIN genres g ON mg.genre_id = g.genre_id
WHERE m.status = 'now-showing'
GROUP BY m.movie_id
";

$result = $conn->query($sql);
?>

<div class="carousel-container">

<?php if ($result && $result->num_rows > 0): ?>

    <?php while ($movie = $result->fetch_assoc()): 

        $poster = !empty($movie['poster'])
            ? $movie['poster']
            : 'https://via.placeholder.com/1920x1080/800020/fff?text=' . urlencode($movie['title']);
    ?>

    <div class="slide">

        <img src="<?= htmlspecialchars($poster) ?>" class="carousel-image">

        <div class="slide-overlay">

            <div class="now-label">Now Showing</div>

            <h2 class="movie-title">
                <?= htmlspecialchars($movie['title']) ?>
            </h2>

            <!-- GENRES -->
            <div class="genre-container">
                <?php if (!empty($movie['genres'])): ?>
                    <?php foreach (explode(',', $movie['genres']) as $genre): ?>
                        <span class="genre-tag">
                            <?= htmlspecialchars(trim($genre)) ?>
                        </span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- DESCRIPTION -->
            <div class="movie-description-box">
                <p class="movie-description">
                    <?= htmlspecialchars($movie['description']) ?>
                </p>
            </div>

        </div>

    </div>

    <?php endwhile; ?>

<?php endif; ?>

    <!-- NAV BUTTONS (ONLY ONCE) -->
    <button class="carousel-btn prev" onclick="moveCarousel(-1)">&#10094;</button>
    <button class="carousel-btn next" onclick="moveCarousel(1)">&#10095;</button>

    <!-- DOTS -->
    <div class="carousel-dots"></div>

</div>
<script>
let currentIndex = 0;

function getSlides() {
    return document.querySelectorAll(".slide");
}

function showSlide(index) {
    const slides = getSlides();

    if (slides.length === 0) return;

    // wrap around
    if (index >= slides.length) currentIndex = 0;
    if (index < 0) currentIndex = slides.length - 1;

    slides.forEach((slide, i) => {
        slide.style.display = (i === currentIndex) ? "block" : "none";
    });
}

function moveCarousel(step) {
    const slides = getSlides();

    if (slides.length === 0) return;

    currentIndex += step;

    if (currentIndex >= slides.length) currentIndex = 0;
    if (currentIndex < 0) currentIndex = slides.length - 1;

    showSlide(currentIndex);
}

// init
document.addEventListener("DOMContentLoaded", () => {
    const slides = getSlides();

    if (slides.length > 0) {
        currentIndex = 0;
        showSlide(currentIndex);
    }
});
// AUTO SLIDE
setInterval(() => {
    moveCarousel(1);
}, 4000); // changes slide every 4 seconds

</script>