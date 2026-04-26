<?php
session_start();

// messages
$login_error = $_SESSION['login_error'] ?? '';
$register_error = $_SESSION['register_error'] ?? '';
$register_success = $_SESSION['register_success'] ?? '';

$open_login = isset($_GET['open']) && $_GET['open'] === 'login';

// clear messages
unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['register_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema - Login & Register</title>

    <!-- KEEP YOUR ORIGINAL DESIGN FILES -->
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="logres.css">
</head>

<body>

<!-- MESSAGES (UNCHANGED STYLE) -->
<?php if ($login_error): ?>
    <div class="message error-message"><?= htmlspecialchars($login_error) ?></div>
<?php endif; ?>

<?php if ($register_error): ?>
    <div class="message error-message"><?= htmlspecialchars($register_error) ?></div>
<?php endif; ?>

<?php if ($register_success): ?>
    <div class="message success-message"><?= htmlspecialchars($register_success) ?></div>
<?php endif; ?>

<!-- HEADER (UNCHANGED DESIGN) -->
<header class="main-header">
    <div class="header-container">

        <div class="logo-section">
            <h1 class="logo">Cinema</h1>
        </div>

        <nav class="auth-nav">
            <button class="auth-btn login-btn" onclick="openLogin()">Login</button>
            <button class="auth-btn register-btn" onclick="openRegister()">Register</button>
        </nav>

    </div>
</header>

<!-- MAIN CONTENT -->
<main class="main-content">
    <?php include 'nowshowing.php'; ?>
</main>

<!-- MODALS (LOGIN + REGISTER INSIDE logres.php) -->
<?php include 'logres.php'; ?>

<!-- =========================
     JS CONTROLLER (FIXED)
========================= -->
<script>

// OPEN MODALS
function openLogin() {
    showLoginModal();
}

function openRegister() {
    showRegisterModal();
}

// CLOSE MODALS
function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('show');
}

function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('show');
}

// AUTO LOGIN AFTER REGISTER
window.addEventListener("DOMContentLoaded", function () {

    const shouldOpenLogin = <?= $open_login ? 'true' : 'false' ?>;

    if (shouldOpenLogin) {

        const loginModal = document.getElementById('loginModal');
        if (loginModal) {
            loginModal.style.display = 'block';
        }

        const loginError = document.getElementById('loginError');
        if (loginError && "<?= $register_success ?>") {
            loginError.style.display = 'block';
            loginError.style.background = "#2ecc71";
            loginError.textContent = "<?= $register_success ?>";
        }
    }

});
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

window.addEventListener('load', () => {
    window.scrollTo(0, 0);
});
</script>

</body>
</html>