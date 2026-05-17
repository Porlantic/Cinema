<?php
session_start();

$login_error = $_SESSION['login_error'] ?? '';
$register_error = $_SESSION['register_error'] ?? '';
$register_success = $_SESSION['register_success'] ?? '';

$open_login = isset($_GET['open']) && $_GET['open'] === 'login';

unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['register_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cinema</title>

<link rel="stylesheet" href="index.css">
<link rel="stylesheet" href="nowshowing.css">
<link rel="stylesheet" href="logres.css">
</head>

<body>

<!-- HEADER -->
<header class="main-header">
    <div class="header-container">

        <div class="logo-section">
            <h1 class="logo">Cinema</h1>
        </div>

        <nav class="auth-nav">
            <button class="auth-btn" onclick="openLogin()">Login</button>
            <button class="auth-btn" onclick="openRegister()">Register</button>
        </nav>

    </div>
</header>

<!-- MAIN LAYOUT -->
<main class="main-content">
    <?php include 'nowshowing.php'; ?>
</main>

<?php include 'logres.php'; ?>

<script>
function openLogin() {
    showLoginModal();
}

function openRegister() {
    showRegisterModal();
}

if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

window.addEventListener('load', () => {
    window.scrollTo(0, 0);
});
</script>

</body>
</html>