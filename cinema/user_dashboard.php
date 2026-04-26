<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Dashboard</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #0b0b0b;
    color: #fff;
}

/* ================= NAVBAR ================= */
.navbar {
    background: linear-gradient(135deg, #800020, #4b0e0e);
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
}

.logo {
    font-size: 22px;
    font-weight: bold;
    color: #ffcc00;
    letter-spacing: 2px;
}

.nav-links a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    transition: 0.3s;
}

.nav-links a:hover {
    color: #ffcc00;
}

/* PROFILE DROPDOWN */
.profile-dropdown {
    position: relative;
}

.profile-btn {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background: #111;
    min-width: 150px;
    border-radius: 5px;
    overflow: hidden;
}

.dropdown-content a {
    display: block;
    padding: 10px;
    color: white;
    text-decoration: none;
}

.dropdown-content a:hover {
    background: #800020;
}

.profile-dropdown:hover .dropdown-content {
    display: block;
}

/* ================= SECTIONS ================= */
.section {
    display: none;
    padding: 30px;
}

.section.active {
    display: block;
}

/* MOVIE GRID */
.movie-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.movie-card {
    background: #111;
    padding: 15px;
    border-radius: 10px;
}

.movie-card h3 {
    color: #ffcc00;
    margin-bottom: 10px;
}

.movie-card button {
    margin-top: 10px;
    padding: 8px;
    background: #800020;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
}

.movie-card button:hover {
    background: #a00028;
}
</style>

</head>

<body>

<!-- NAVBAR -->
<header class="navbar">

    <div class="logo">Cinema</div>

    <nav class="nav-links">
        <a href="#" onclick="showSection('movies')">Movies</a>
        <a href="#" onclick="showSection('bookings')">My Bookings</a>
    </nav>

    <div class="profile-dropdown">

        <button class="profile-btn">
            <?= htmlspecialchars($_SESSION['user_name']) ?> ▼
        </button>

        <div class="dropdown-content">
            <a href="#" onclick="showSection('profile')">Profile</a>
            <a href="logout.php">Logout</a>
        </div>

    </div>

</header>

<!-- MOVIES -->
<div id="movies" class="section active">
    <?php include 'user_movies.php'; ?>
</div>

<!-- BOOKINGS -->
<div id="bookings" class="section">
    <h2>My Bookings</h2>
    <p style="color:#888;">No bookings yet</p>
</div>

<!-- PROFILE -->
<div id="profile" class="section">

    <h2>My Profile</h2>

    <p><b>Name:</b> <?= htmlspecialchars($_SESSION['user_name']) ?></p>
    <p><b>Email:</b> <?= htmlspecialchars($_SESSION['user_email']) ?></p>
    <p><b>User ID:</b> <?= $_SESSION['user_id'] ?></p>
    <p><b>Role:</b> <?= $_SESSION['user_role'] ?></p>

</div>

<script>
function showSection(section) {
    document.querySelectorAll('.section').forEach(s => {
        s.classList.remove('active');
    });

    document.getElementById(section).classList.add('active');
}
</script>

</body>
</html>