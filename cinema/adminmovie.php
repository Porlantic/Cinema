
<?php require_once 'config.php'; ?>

<div class="section-header">

    <button class="btn btn-primary" onclick="openAddMovieModal()">
        Add New Movie
    </button>
</div>

<div class="movie-tabs">

    <!-- TABS -->
    <div class="tab-nav">
        <button class="tab-btn active" onclick="showMovieTab('now-showing', event)">
            Now Showing
        </button>

        <button class="tab-btn" onclick="showMovieTab('scheduled', event)">
            Scheduled
        </button>
    </div>

    <!-- NOW SHOWING -->
    <div id="now-showing-tab" class="tab-content active">
        <div class="movie-grid">

        <?php
        $result = $conn->query("SELECT * FROM movies WHERE status='now-showing' ORDER BY movie_id DESC");

        while ($movie = $result->fetch_assoc()) {

            $id = $movie['movie_id'];

            $poster = !empty($movie['poster'])
                ? $movie['poster']
                : 'https://via.placeholder.com/200x300/800020/ffffff?text=' . urlencode($movie['title']);
        ?>

            <div class="movie-card">

    <div class="movie-poster">
        <img src="<?= $poster ?>">
    </div>

    <div class="movie-info">

        <!-- ✅ TITLE (NOW PROMINENT) -->
        <h3 class="movie-title-main">
            <?= htmlspecialchars($movie['title']) ?>
        </h3>

        <small class="movie-date">
            <?= !empty($movie['datetime'])
                ? date('M d, Y • h:i A', strtotime($movie['datetime']))
                : 'No schedule' ?>
        </small>

        <p><?= htmlspecialchars($movie['description']) ?></p>

        <div class="movie-price">
            ₱<?= number_format($movie['price'], 2) ?>
        </div>

        <div class="movie-actions">
            <button class="btn-sm btn-edit" onclick="editMovie(<?= $id ?>)">Edit</button>
            <button class="btn-sm btn-delete" onclick="deleteMovie(<?= $id ?>)">Delete</button>
        </div>

    </div>

</div>

        <?php } ?>

        </div>
    </div>

    <!-- SCHEDULED -->
    <div id="scheduled-tab" class="tab-content">
        <div class="movie-grid">

        <?php
        $result = $conn->query("SELECT * FROM movies WHERE status='scheduled' ORDER BY movie_id DESC");

        while ($movie = $result->fetch_assoc()) {

            $id = $movie['movie_id'];

            $poster = !empty($movie['poster'])
                ? $movie['poster']
                : 'https://via.placeholder.com/200x300/ff6b35/ffffff?text=' . urlencode($movie['title']);
        ?>

            <div class="movie-card">
                <div class="movie-poster">
                    <img src="<?= $poster ?>">
                </div>

                <div class="movie-info">

                    <h4><?= htmlspecialchars($movie['title']) ?></h4>

                    <small class="movie-date">
                        <?= !empty($movie['datetime'])
                            ? date('M d, Y • h:i A', strtotime($movie['datetime']))
                            : 'No schedule' ?>
                    </small>

                    <p><?= htmlspecialchars($movie['description']) ?></p>

                    <div class="movie-price">
                        ₱<?= number_format($movie['price'], 2) ?>
                    </div>

                    <div class="movie-actions">
                        <button class="btn-sm btn-edit" onclick="editMovie(<?= $id ?>)">Edit</button>
                        <button class="btn-sm btn-delete" onclick="deleteMovie(<?= $id ?>)">Delete</button>
                    </div>

                </div>
            </div>

        <?php } ?>

        </div>
    </div>

</div>
</section>

<!-- MODAL -->
<div id="movieModal" class="modal">

<div class="modal-content">

    <div class="modal-header">
        <h3 id="modalTitle">Add Movie</h3>
        <span class="close-btn" onclick="closeMovieModal()">×</span>
    </div>

    <form id="movieForm" enctype="multipart/form-data" onsubmit="saveMovie(event)">

        <input type="hidden" name="movie_id" id="movie_id">

        <input type="text" name="title" placeholder="Title" required>

        <textarea name="description" placeholder="Description"></textarea>

        <div class="price-wrapper">
            <span class="peso-sign">₱</span>
            <input type="text" name="price" placeholder="0.00" required>
        </div>

        <input type="date" name="date">
        <input type="time" name="time">

        <div class="file-upload">
            <label class="file-btn">Insert Image</label>
            <input type="file" name="poster" onchange="showFileName(this)">
            <div class="file-status">No image selected</div>
        </div>

        <select name="status">
            <option value="now-showing">Now Showing</option>
            <option value="scheduled">Scheduled</option>
        </select>

        <div class="modal-actions">
            <button type="submit">Save</button>
            <button type="button" onclick="closeMovieModal()">Cancel</button>
        </div>

    </form>

</div>
</div>

<script>

// OPEN MODAL
function openAddMovieModal() {
    document.getElementById('movieForm').reset();
    document.getElementById('movie_id').value = '';
    document.getElementById('modalTitle').innerText = "Add Movie";
    document.getElementById('movieModal').style.display = 'flex';
}

// CLOSE MODAL
function closeMovieModal() {
    document.getElementById('movieModal').style.display = 'none';
}

// TAB SWITCH
function showMovieTab(tab, event) {

    document.querySelectorAll('.tab-content').forEach(t => {
        t.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active');
    });

    document.getElementById(tab + '-tab').classList.add('active');

    if (event) {
        event.target.classList.add('active');
    }
}

// EDIT MOVIE (FIXED ₱ + FORMAT)
function editMovie(id) {

    fetch('get_movie.php?id=' + id)
    .then(res => res.json())
    .then(data => {

        document.getElementById('movie_id').value = data.movie_id;
        document.querySelector('input[name="title"]').value = data.title;
        document.querySelector('textarea[name="description"]').value = data.description;
        document.querySelector('select[name="status"]').value = data.status;

        // PRICE FORMAT FIX
        let rawPrice = parseFloat(data.price || 0);

        let formattedPrice = rawPrice.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        document.querySelector('input[name="price"]').value = formattedPrice;

        document.getElementById('modalTitle').innerText = "Edit Movie";
        document.getElementById('movieModal').style.display = 'flex';
    });
}

// PRICE FORMAT INPUT
const priceInput = document.querySelector('input[name="price"]');

if (priceInput) {

    priceInput.addEventListener('input', function (e) {

        let value = e.target.value.replace(/,/g, '');
        value = value.replace(/[^0-9.]/g, '');

        let parts = value.split('.');

        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");

        if (parts[1]) {
            parts[1] = parts[1].slice(0, 2);
        }

        e.target.value = parts.join('.');
    });
}

// SAVE MOVIE
function saveMovie(event) {
    event.preventDefault();

    const form = new FormData(document.getElementById('movieForm'));

    let price = form.get("price");
    if (price) {
        form.set("price", price.replace(/,/g, ''));
    }

    fetch('save_movie.php', {
        method: 'POST',
        body: form
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) location.reload();
        else alert(res.error);
    });
}

// DELETE MOVIE
function deleteMovie(id) {
    if (!confirm("Delete this movie?")) return;

    fetch('delete_movie.php?id=' + id)
    .then(res => res.json())
    .then(res => {
        if (res.success) location.reload();
        else alert(res.error);
    });
}

// CLOSE MODAL OUTSIDE CLICK
window.onclick = function(e) {
    const modal = document.getElementById('movieModal');
    if (e.target === modal) closeMovieModal();
};

</script>