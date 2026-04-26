<link rel="stylesheet" href="nowshowing.css">

<section class="now-showing">
    <div class="carousel-container">

        <div class="carousel-track">

        <?php
        require_once 'config.php';

        $sql = "SELECT * FROM movies WHERE status='now-showing'";
        $result = $conn->query($sql);

        $totalSlides = ($result && $result->num_rows > 0) ? $result->num_rows : 1;

        if ($result && $result->num_rows > 0) {

            while ($movie = $result->fetch_assoc()) {

                $poster = !empty($movie['poster'])
                    ? $movie['poster']
                    : 'https://via.placeholder.com/1920x1080/800020/ffffff?text=' . urlencode($movie['title']);
        ?>

            <div class="slide">
                <img src="<?= htmlspecialchars($poster) ?>" class="carousel-image">

                <div class="slide-overlay">
                    <h3><?= htmlspecialchars($movie['title']) ?></h3>
                    <p><?= htmlspecialchars($movie['description']) ?></p>

                    <div class="movie-price">
                        ₱<?= number_format($movie['price'], 2) ?>
                    </div>
                </div>
            </div>

        <?php
            }
        } else {
        ?>

            <div class="slide">
                <img src="https://via.placeholder.com/1920x1080/800020/ffffff?text=No+Movies" class="carousel-image">
            </div>

        <?php } ?>

        </div>

        <!-- NAV BUTTONS -->
        <button class="carousel-btn prev-btn" onclick="moveCarousel(-1)">&#10094;</button>
        <button class="carousel-btn next-btn" onclick="moveCarousel(1)">&#10095;</button>

        <!-- DOTS -->
        <div class="carousel-dots">
            <?php for ($i = 1; $i <= $totalSlides; $i++): ?>
                <span class="dot" onclick="currentSlide(<?= $i ?>)"></span>
            <?php endfor; ?>
        </div>

    </div>
</section>

<script>

let currentSlideIndex = 1;
let autoSlideInterval;

function initCarousel() {
    showSlide(currentSlideIndex);
    startAutoSlide();
}

function moveCarousel(direction) {
    currentSlideIndex += direction;
    showSlide(currentSlideIndex);
    resetAutoSlide();
}

function currentSlide(n) {
    currentSlideIndex = n;
    showSlide(currentSlideIndex);
    resetAutoSlide();
}

function showSlide(n) {

    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    if (slides.length === 0) return;

    if (n > slides.length) currentSlideIndex = 1;
    if (n < 1) currentSlideIndex = slides.length;

    slides.forEach(slide => slide.style.display = "none");
    dots.forEach(dot => dot.classList.remove('active'));

    slides[currentSlideIndex - 1].style.display = "block";

    if (dots[currentSlideIndex - 1]) {
        dots[currentSlideIndex - 1].classList.add('active');
    }
}

function startAutoSlide() {
    autoSlideInterval = setInterval(() => {
        currentSlideIndex++;
        showSlide(currentSlideIndex);
    }, 5000);
}

function resetAutoSlide() {
    clearInterval(autoSlideInterval);
    startAutoSlide();
}

document.addEventListener('DOMContentLoaded', initCarousel);

</script>