<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0", false);
header('Pragma: no-cache');
header('Expires: 0');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cinema Admin Dashboard</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="adminmovie.css">
<link rel="stylesheet" href="admin_bookings.css">
<link rel="stylesheet" href="admin_users.css">

<style>
.content-section {
    display: none;
}

.content-section.active {
    display: block;
}

.nav-link {
    cursor: pointer;
}
</style>

</head>

<body>

<div class="admin-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="sidebar-header">
            <h2>Cinema Admin</h2>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-list">

                <li class="nav-item">
                    <a href="#" class="nav-link active"
                       onclick="showSection('movies', this); return false;">
                        Movie List
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link"
                       onclick="showSection('bookings', this); return false;">
                        Booking List
                    </a>
                </li>

                <!-- ✅ NEW USERS TAB -->
                <li class="nav-item">
                    <a href="#" class="nav-link"
                       onclick="showSection('users', this); return false;">
                        User List
                    </a>
                </li>

            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <header class="content-header">
            <h1 id="page-title">Movie List</h1>
        </header>

        <!-- MOVIES -->
        <section id="movies-section" class="content-section active">
            <?php include 'adminmovie.php'; ?>
        </section>

        <!-- BOOKINGS -->
        <section id="bookings-section" class="content-section">
            <?php include 'admin_bookings.php'; ?>
        </section>

        <!-- USERS (NEW) -->
        <section id="users-section" class="content-section">
            <?php include 'admin_users.php'; ?>
        </section>

    </main>

</div>

<script>
function showSection(section, el) {
    sessionStorage.setItem('activeTab', section);

    document.querySelectorAll('.content-section').forEach(sec => {
        sec.classList.remove('active');
    });

    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });

    document.getElementById(section + '-section').classList.add('active');

    if (el) el.classList.add('active');

    const titles = {
        movies: 'Movie List',
        bookings: 'Booking List',
        users: 'User List'
    };

    document.getElementById('page-title').textContent = titles[section];
}
</script>

<script>
window.addEventListener("pageshow", function (event) {
    if (event.persisted || performance.navigation.type === 2) {
        window.location.replace("index.php");
    }
});
window.addEventListener('load', () => {
    const tab = sessionStorage.getItem('activeTab');

    if (tab) {
        showSection(tab);
    }
});
</script>

</body>
</html>