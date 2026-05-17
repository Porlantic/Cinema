<?php require_once 'config.php'; ?>

<div class="section-header">
    <div class="movie-actions">
        <button class="btn btn-primary" onclick="openAddMovieModal()">
            Add New Movie
        </button>
        <button class="gear-btn" onclick="openGenreModal()">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12,15.5A3.5,3.5 0 0,1 8.5,12A3.5,3.5 0 0,1 12,8.5A3.5,3.5 0 0,1 15.5,12A3.5,3.5 0 0,1 12,15.5M19.43,12.97C19.47,12.65 19.5,12.33 19.5,12C19.5,11.67 19.47,11.34 19.43,11L21.54,9.37C21.73,9.18 21.78,8.9 21.66,8.66L19.66,5.34C19.54,5.1 19.27,5 19,5.08L16.56,5.92C16.04,5.5 15.5,5.17 14.87,4.93L14.5,2.42C14.46,2.18 14.25,2 14,2H10C9.75,2 9.54,2.18 9.5,2.42L9.13,4.93C8.5,5.17 7.96,5.5 7.44,5.92L5,5.08C4.73,5 4.46,5.1 4.34,5.34L2.34,8.66C2.21,8.9 2.27,9.18 2.46,9.37L4.57,11C4.53,11.34 4.5,11.67 4.5,12C4.5,12.33 4.53,12.65 4.57,12.97L2.46,14.63C2.27,14.82 2.21,15.1 2.34,15.34L4.34,18.66C4.46,18.9 4.73,19 5,18.92L7.44,18.08C7.96,18.5 8.5,18.83 9.13,19.07L9.5,21.58C9.54,21.82 9.75,22 10,22H14C14.25,22 14.46,21.82 14.5,21.58L14.87,19.07C15.5,18.83 16.04,18.5 16.56,18.08L19,18.92C19.27,19 19.54,18.9 19.66,18.66L21.66,15.34C21.78,15.1 21.73,14.82 21.54,14.63L19.43,12.97Z"/>
            </svg>
        </button>
    </div>
</div>

<!-- TABS -->
<div class="tab-nav">
    <button class="tab-btn active" onclick="showMovieTab('now-showing', event)">
        Now Showing
    </button>
    <button class="tab-btn" onclick="showMovieTab('scheduled', event)">
        Coming Soon
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
                    <h3 class="movie-title-main">
                        <?= htmlspecialchars($movie['title']) ?>
                    </h3>
                    <small class="movie-date">
                        <?= !empty($movie['show_date'])
                            ? date('M d, Y', strtotime($movie['show_date'])) . ' • ' . ($movie['show_time'] ?? 'No time')
                            : 'No schedule' ?>
                    </small>
                    <div class="movie-actions">
                        <button class="btn-sm btn-view" onclick="viewMovie(
                            '<?= htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8') ?>',
                            '<?= htmlspecialchars($movie['description'], ENT_QUOTES, 'UTF-8') ?>',
                            '<?= !empty($movie['show_date']) ? date('M d, Y', strtotime($movie['show_date'])) . ' • ' . ($movie['show_time'] ?? '') : 'No schedule' ?>',
                            '<?= htmlspecialchars($movie['price'], ENT_QUOTES, 'UTF-8') ?>',
                            '<?= htmlspecialchars($movie['status'], ENT_QUOTES, 'UTF-8') ?>'
                        )">View</button>
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
                        <?= !empty($movie['show_date'])
                            ? date('M d, Y', strtotime($movie['show_date'])) . ' • ' . ($movie['show_time'] ?? 'No time')
                            : 'No schedule' ?>
                    </small>
                    <div class="movie-actions">
                        <button class="btn-sm btn-view" onclick="viewMovie(
                            '<?= htmlspecialchars($movie['title'], ENT_QUOTES, 'UTF-8') ?>',
                            '<?= htmlspecialchars($movie['description'], ENT_QUOTES, 'UTF-8') ?>',
                            '<?= !empty($movie['show_date']) ? date('M d, Y', strtotime($movie['show_date'])) . ' • ' . ($movie['show_time'] ?? '') : 'No schedule' ?>',
                            '<?= htmlspecialchars($movie['price'], ENT_QUOTES, 'UTF-8') ?>',
                            '<?= htmlspecialchars($movie['status'], ENT_QUOTES, 'UTF-8') ?>'
                        )">View</button>
                        <button class="btn-sm btn-edit" onclick="editMovie(<?= $id ?>)">Edit</button>
                        <button class="btn-sm btn-delete" onclick="deleteMovie(<?= $id ?>)">Delete</button>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- ================= MOVIE MODAL ================= -->
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
            
            <div class="genre-wrapper">
                <div id="selectedGenres" class="genre-box">
                    <span class="placeholder">No genres selected</span>
                </div>
                <select id="genreDropdown">
                    <option value="">Select Genre</option>
                    <?php
                    $genres = $conn->query("SELECT * FROM genres");
                    while ($g = $genres->fetch_assoc()) {
                        echo "<option value='{$g['genre_id']}'>{$g['name']}</option>";
                    }
                    ?>
                </select>
                <input type="hidden" id="genreInput" name="genres">
            </div>
            
            <div class="price-wrapper">
                <span class="peso-sign">₱</span>
                <input type="text" name="price" placeholder="0.00" required>
            </div>
            
            <input type="date" name="date">
            <select name="time">
                <option value="">Select Showtime</option>
                <option value="11:30 AM">11:30 AM</option>
                <option value="2:00 PM">2:00 PM</option>
                <option value="5:30 PM">5:30 PM</option>
            </select>
            
            <div class="file-upload">
                <label class="file-btn">
                    <input type="file" name="poster" onchange="showFileName(this)">
                    <div class="file-status"></div>
                </label>
            </div>
            
            <select name="status">
                <option value="now-showing">Now Showing</option>
                <option value="scheduled">Coming Soon</option>
            </select>
            
            <div class="modal-actions">
                <button type="submit">Save</button>
                <button type="button" onclick="closeMovieModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= VIEW MOVIE MODAL ================= -->
<div id="viewMovieModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Movie Details</h3>
            <span class="close-btn" onclick="closeViewModal()">×</span>
        </div>
        <div class="view-body">
            <p><strong>Title:</strong> <span id="viewTitle"></span></p>
            <p><strong>Date & Time:</strong></p>
            <p id="viewDate"></p>
            <p><strong>Price:</strong> ₱<span id="viewPrice"></span></p>
            <p><strong>Genre:</strong> <span id="viewGenre"></span></p>
            <p><strong>Status:</strong> <span id="viewStatus"></span></p>
        </div>
    </div>
</div>

<!-- ================= GENRE MANAGEMENT MODAL ================= -->
<div id="genreModal" class="genre-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Manage Genres</h2>
            <span class="close-btn" onclick="closeGenreModal()">×</span>
        </div>
        <div class="modal-body">
            <!-- Container 1: Genre List -->
            <div class="genre-container">
                <h3>Existing Genres</h3>
                <div id="genreList" class="genre-list">
                    <!-- Genres will be loaded here -->
                </div>
            </div>
            
            <!-- Container 2: Add Genre -->
            <div class="genre-container">
                <h3>Add New Genre</h3>
                <div class="add-genre-form">
                    <input type="text" id="newGenre" placeholder="Enter genre name">
                    <button onclick="addGenre()">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ================= GENRE SELECTION FOR MOVIES =================
let selectedGenres = [];

document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.getElementById("genreDropdown");
    if (dropdown) {
        dropdown.addEventListener("change", function () {
            const id = this.value;
            if (!id || selectedGenres.includes(id)) return;
            selectedGenres.push(id);
            renderGenres();
            this.value = "";
        });
    }
});

function renderGenres() {
    const container = document.getElementById("selectedGenres");
    container.innerHTML = "";
    
    selectedGenres.forEach(id => {
        const tag = document.createElement("div");
        tag.className = "genre-tag";
        tag.innerHTML = `
            ${getGenreName(id)}
            <span onclick="removeGenre('${id}')">×</span>
        `;
        container.appendChild(tag);
    });
    
    document.getElementById("genreInput").value = selectedGenres.join(",");
}

function removeGenre(id) {
    selectedGenres = selectedGenres.filter(g => g !== id);
    renderGenres();
}

function getGenreName(id) {
    const opts = document.getElementById("genreDropdown").options;
    for (let o of opts) {
        if (o.value === id) return o.text;
    }
    return "";
}

// ================= GENRE MANAGEMENT MODAL =================
function openGenreModal() {
    document.getElementById("genreModal").classList.add("show");
    loadGenres();
}

function closeGenreModal() {
    document.getElementById("genreModal").classList.remove("show");
}

// Load existing genres
function loadGenres() {
    fetch('manage_genres.php')
        .then(res => res.json())
        .then(genres => {
            const genreList = document.getElementById('genreList');
            genreList.innerHTML = '';
            
            genres.forEach(genre => {
                const item = document.createElement('div');
                item.className = 'genre-item';
                item.innerHTML = `
                    <span class="genre-item-name">${genre.name}</span>
                    <button class="genre-item-delete" onclick="deleteGenre(${genre.genre_id})">×</button>
                `;
                genreList.appendChild(item);
            });
        })
        .catch(error => console.error('Error loading genres:', error));
}

// Add new genre
function addGenre() {
    const name = document.getElementById('newGenre').value.trim();
    
    if (!name) {
        alert('Please enter a genre name');
        return;
    }
    
    fetch('manage_genres.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=add&name=${encodeURIComponent(name)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('newGenre').value = '';
            loadGenres();
            updateGenreDropdown();
        } else {
            alert(data.error || 'Failed to add genre');
        }
    })
    .catch(error => console.error('Error adding genre:', error));
}

// Delete genre
function deleteGenre(genreId) {
    if (!confirm('Are you sure you want to delete this genre?')) {
        return;
    }
    
    fetch('manage_genres.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `action=delete&genre_id=${genreId}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadGenres();
            updateGenreDropdown();
        } else {
            alert(data.error || 'Failed to delete genre');
        }
    })
    .catch(error => console.error('Error deleting genre:', error));
}

// Update genre dropdown after add/delete
function updateGenreDropdown() {
    fetch('manage_genres.php')
        .then(res => res.json())
        .then(genres => {
            const dropdown = document.getElementById('genreDropdown');
            const currentValue = dropdown.value;
            
            dropdown.innerHTML = '<option value="">Select Genre</option>';
            
            genres.forEach(genre => {
                const option = document.createElement('option');
                option.value = genre.genre_id;
                option.textContent = genre.name;
                dropdown.appendChild(option);
            });
            
            dropdown.value = currentValue;
        })
        .catch(error => console.error('Error updating dropdown:', error));
}

// ================= TAB SWITCH =================
function showMovieTab(tab, event) {
    document.querySelectorAll('.tab-content').forEach(t => {
        t.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active');
    });
    document.getElementById(tab + '-tab').classList.add('active');
    if (event) event.target.classList.add('active');
}

// ================= MODAL =================
function openAddMovieModal() {
    document.getElementById('movieForm').reset();
    document.getElementById('movie_id').value = '';
    document.getElementById('modalTitle').innerText = "Add Movie";
    document.getElementById('movieModal').style.display = 'flex';
    selectedGenres = [];
    renderGenres();
}

function closeMovieModal() {
    document.getElementById('movieModal').style.display = 'none';
}

// ================= EDIT =================
function editMovie(id) {
    fetch('get_movie.php?id=' + id)
    .then(res => res.json())
    .then(data => {
        document.getElementById('movie_id').value = data.movie_id;
        document.querySelector('input[name="title"]').value = data.title;
        document.querySelector('textarea[name="description"]').value = data.description;
        document.querySelector('select[name="status"]').value = data.status;
        
        let rawPrice = parseFloat(data.price || 0);
        document.querySelector('input[name="price"]').value =
            rawPrice.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        
        if (data.show_date) {
            document.querySelector('input[name="date"]').value = data.show_date;
        }
        if (data.show_time) {
            document.querySelector('select[name="time"]').value = data.show_time;
        }
        
        document.getElementById('modalTitle').innerText = "Edit Movie";
        document.getElementById('movieModal').style.display = 'flex';
    });
}

// ================= SAVE =================
function saveMovie(event) {
    event.preventDefault();
    
    const form = new FormData(document.getElementById('movieForm'));
    
    let price = form.get("price");
    if (price) form.set("price", price.replace(/,/g, ''));
    
    fetch('save_movie.php', {
        method: 'POST',
        body: form
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            closeMovieModal();
            location.reload();
        } else {
            alert(res.error);
        }
    });
}

// ================= DELETE =================
function deleteMovie(id) {
    if (!confirm("Delete this movie?")) return;
    
    fetch('delete_movie.php?id=' + id)
    .then(res => res.json())
    .then(res => {
        if (res.success) location.reload();
        else alert(res.error);
    });
}

// ================= VIEW =================
function viewMovie(title, description, schedule, price, status) {
    document.getElementById("viewTitle").innerText = title;
    document.getElementById("viewDate").innerText = schedule || "No schedule";
    document.getElementById("viewPrice").innerText = price;
    document.getElementById("viewStatus").innerText = status;
    
    fetch('get_movie_genres.php?title=' + encodeURIComponent(title))
        .then(res => res.json())
        .then(data => {
            if (data.success && data.genres) {
                document.getElementById("viewGenre").innerText = data.genres;
            } else {
                document.getElementById("viewGenre").innerText = "No genres";
            }
        })
        .catch(error => {
            document.getElementById("viewGenre").innerText = "Error loading genres";
        });
    
    document.getElementById("viewMovieModal").style.display = "flex";
}

function closeViewModal() {
    document.getElementById("viewMovieModal").style.display = "none";
}

// ================= CLOSE OUTSIDE CLICK =================
window.onclick = function(e) {
    const movieModal = document.getElementById('movieModal');
    const viewModal = document.getElementById('viewMovieModal');
    
    if (e.target === movieModal) closeMovieModal();
    if (e.target === viewModal) closeViewModal();
};
</script>
