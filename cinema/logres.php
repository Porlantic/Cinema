<?php
session_start();
require_once 'config.php';

/* =========================
   REGISTER HANDLER
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_name'])) {

    $name = trim($_POST['register_name']);
    $email = trim($_POST['register_email']);
    $password = $_POST['register_password'];
    $confirm = $_POST['register_confirm_password'];

    if (!preg_match('/[a-zA-Z]/', $name)) {
        $_SESSION['register_error'] = "Name must contain letters.";
        $_SESSION['open_register_modal'] = true;
        header("Location: index.php");
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['register_error'] = "Password must be at least 8 characters long.";
        $_SESSION['open_register_modal'] = true;
        header("Location: index.php");
        exit();
    }

    if (!preg_match('/[A-Za-z]/', $password)) {
        $_SESSION['register_error'] = "Password must contain at least one letter.";
        $_SESSION['open_register_modal'] = true;
        header("Location: index.php");
        exit();
    }

    if (!preg_match('/[0-9]/', $password)) {
        $_SESSION['register_error'] = "Password must contain at least one number.";
        $_SESSION['open_register_modal'] = true;
        header("Location: index.php");
        exit();
    }

    if ($password !== $confirm) {
        $_SESSION['register_error'] = "Passwords do not match.";
        $_SESSION['open_register_modal'] = true;
        header("Location: index.php");
        exit();
    }

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['register_error'] = "All fields are required.";
        $_SESSION['open_register_modal'] = true;
        header("Location: index.php");
        exit();
    }

    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        $_SESSION['open_register_modal'] = true;
        header("Location: index.php");
        exit();
    }

    $name = strtoupper($name);
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (name, email, password, role, status)
        VALUES (?, ?, ?, 'user', 'active')
    ");
    $stmt->bind_param("sss", $name, $email, $hashed);
    $stmt->execute();

    $_SESSION['register_success'] = "Registration successful! You can now login.";
    $_SESSION['open_register_modal'] = true;
    header("Location: index.php");
    exit();
}
?>

<!-- =========================
     LOGIN MODAL
========================= -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Login</h2>

        <form id="loginForm">
            <div id="loginError" class="login-error" style="display:none;"></div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="login_email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <button type="button" class="toggle-btn" onclick="togglePassword('loginPassword', this)">Show</button>
                <input type="password" name="login_password" id="loginPassword" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p style="text-align:center; margin-top:15px;">
            <a href="#" onclick="showForgotModal()" style="color:rgba(255,255,255,0.5); font-size:0.85rem; text-decoration:none; transition:0.2s;" onmouseover="this.style.color='#e50914'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">Forgot Password?</a>
        </p>
    </div>
</div>

<!-- =========================
     REGISTER MODAL
========================= -->
<div id="registerModal" <?php echo isset($_SESSION['open_register_modal']) ? 'class="modal show"' : 'class="modal"'; ?>>

    <div class="modal-content">
        <span class="close" onclick="closeRegisterModal()">&times;</span>
        <h2>Register</h2>

        <?php if (isset($_SESSION['register_success'])): ?>
            <div class="alert success">
                <?php 
                    echo $_SESSION['register_success'];
                    unset($_SESSION['register_success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['register_error'])): ?>
            <div class="alert error">
                <?php 
                    echo $_SESSION['register_error'];
                    unset($_SESSION['register_error']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="logres.php">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text"
                       name="register_name"
                       id="registerName"
                       required
                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '')">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="register_email" required>
            </div>

            <div class="form-group">
                <label>Password</label>

                <button type="button"
                        class="toggle-btn"
                        onclick="togglePassword('registerPassword', this)">
                    Show
                </button>

                <input type="password"
                       name="register_password"
                       id="registerPassword"
                       placeholder="Enter password"
                       required>

                <!-- 🔥 PASSWORD RULES (NOW VISIBLE UNDER INPUT) -->
                <small class="password-rules">
                    <span id="rule-capital">• At least 1 capital letter</span>
                    <span id="rule-length">• 8 characters minimum</span>
                    <span id="rule-number">• Should include numbers</span>
                    <span id="rule-special">• Should include special character (_!@#$%)</span>
                </small>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>

                <button type="button"
                        class="toggle-btn"
                        onclick="togglePassword('registerConfirmPassword', this)">
                    Show
                </button>

                <input type="password"
                       name="register_confirm_password"
                       id="registerConfirmPassword"
                       placeholder="Confirm password"
                       required>
            </div>

            <button type="submit" class="btn btn-secondary">Register</button>
        </form>
    </div>
</div>

<?php unset($_SESSION['open_register_modal']); ?>

<!-- =========================
     FORGOT PASSWORD MODAL
========================= -->
<div id="forgotModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeForgotModal()">&times;</span>
        <h2>Forgot Password</h2>

        <!-- STEP 1: Email Verification -->
        <div id="forgotStep1">
            <div id="forgotError" class="alert error" style="display:none;"></div>
            <div class="form-group">
                <label>Enter your email address</label>
                <input type="email" id="forgotEmail" placeholder="your@email.com" required>
            </div>
            <button type="button" class="btn btn-primary" onclick="verifyEmail()">Verify Email</button>
        </div>

        <!-- STEP 2: New Password -->
        <div id="forgotStep2" style="display:none;">
            <div id="resetError" class="alert error" style="display:none;"></div>
            <div id="resetSuccess" class="alert success" style="display:none;"></div>

            <div class="form-group">
                <label>New Password</label>
                <button type="button" class="toggle-btn" onclick="togglePassword('forgotNewPassword', this)">Show</button>
                <input type="password" id="forgotNewPassword" placeholder="Enter new password" required>

                <small class="password-rules">
                    <span id="forgot-rule-capital">• At least 1 capital letter</span>
                    <span id="forgot-rule-length">• 8 characters minimum</span>
                    <span id="forgot-rule-number">• Should include numbers</span>
                    <span id="forgot-rule-special">• Should include special character (_!@#$%)</span>
                </small>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <button type="button" class="toggle-btn" onclick="togglePassword('forgotConfirmPassword', this)">Show</button>
                <input type="password" id="forgotConfirmPassword" placeholder="Confirm new password" required>
            </div>

            <button type="button" class="btn btn-primary" onclick="resetPassword()">Reset Password</button>
        </div>
    </div>
</div>

<!-- =========================
     MODAL CONTROL SCRIPTS
========================= -->
<script>
function showLoginModal() {
    document.getElementById('loginModal').classList.add('show');
}

function closeLoginModal() {
    document.getElementById('loginModal').classList.remove('show');
}

function showRegisterModal() {
    document.getElementById('registerModal').classList.add('show');
}

function closeRegisterModal() {
    document.getElementById('registerModal').classList.remove('show');
}

function showForgotModal() {
    closeLoginModal();
    document.getElementById('forgotStep1').style.display = 'block';
    document.getElementById('forgotStep2').style.display = 'none';
    document.getElementById('forgotEmail').value = '';
    document.getElementById('forgotError').style.display = 'none';
    document.getElementById('forgotModal').classList.add('show');
}

function closeForgotModal() {
    document.getElementById('forgotModal').classList.remove('show');
}

let verifiedEmail = '';

function verifyEmail() {
    const email = document.getElementById('forgotEmail').value.trim();
    const errorDiv = document.getElementById('forgotError');

    if (!email) {
        errorDiv.textContent = 'Please enter your email';
        errorDiv.style.display = 'block';
        return;
    }

    fetch('verify_email.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ email: email })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            verifiedEmail = email;
            document.getElementById('forgotStep1').style.display = 'none';
            document.getElementById('forgotStep2').style.display = 'block';
        } else {
            errorDiv.textContent = data.error || 'Email not found';
            errorDiv.style.display = 'block';
        }
    })
    .catch(() => {
        errorDiv.textContent = 'Something went wrong';
        errorDiv.style.display = 'block';
    });
}

function resetPassword() {
    const newPass = document.getElementById('forgotNewPassword').value;
    const confirmPass = document.getElementById('forgotConfirmPassword').value;
    const errorDiv = document.getElementById('resetError');
    const successDiv = document.getElementById('resetSuccess');

    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';

    if (newPass.length < 8) {
        errorDiv.textContent = 'Password must be at least 8 characters';
        errorDiv.style.display = 'block';
        return;
    }
    if (!/[A-Z]/.test(newPass)) {
        errorDiv.textContent = 'Password must contain at least one capital letter';
        errorDiv.style.display = 'block';
        return;
    }
    if (!/[0-9]/.test(newPass)) {
        errorDiv.textContent = 'Password must contain at least one number';
        errorDiv.style.display = 'block';
        return;
    }
    if (!/[_!@#$%]/.test(newPass)) {
        errorDiv.textContent = 'Password must contain a special character (_!@#$%)';
        errorDiv.style.display = 'block';
        return;
    }
    if (newPass !== confirmPass) {
        errorDiv.textContent = 'Passwords do not match';
        errorDiv.style.display = 'block';
        return;
    }

    fetch('reset_password.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ email: verifiedEmail, password: newPass })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            successDiv.textContent = 'Password reset successful! You can now login.';
            successDiv.style.display = 'block';
            setTimeout(() => {
                closeForgotModal();
                showLoginModal();
            }, 2000);
        } else {
            errorDiv.textContent = data.error || 'Failed to reset password';
            errorDiv.style.display = 'block';
        }
    })
    .catch(() => {
        errorDiv.textContent = 'Something went wrong';
        errorDiv.style.display = 'block';
    });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const forgotModal = document.getElementById('forgotModal');
    
    if (event.target === loginModal) {
        closeLoginModal();
    }
    if (event.target === registerModal) {
        closeRegisterModal();
    }
    if (event.target === forgotModal) {
        closeForgotModal();
    }
}

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;

    if (input.type === "password") {
        input.type = "text";
        btn.textContent = "Hide";
    } else {
        input.type = "password";
        btn.textContent = "Show";
    }
}
</script>

<!-- =========================
     LOGIN AJAX
========================= -->
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const errorDiv = document.getElementById('loginError');
    const btn = this.querySelector('button[type="submit"]');

    btn.disabled = true;
    btn.textContent = "Logging in...";

    fetch('login_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            errorDiv.style.display = 'block';
            errorDiv.textContent = data.message;

            btn.disabled = false;
            btn.textContent = "Login";
        }
    })
    .catch(() => {
        errorDiv.style.display = 'block';
        errorDiv.textContent = "Something went wrong.";

        btn.disabled = false;
        btn.textContent = "Login";
    });
});

</script>   
<!-- =========================
     PASSWORD RULES VALIDATION
========================= -->
<script>
const regPass = document.getElementById("registerPassword");

if (regPass) {
    regPass.addEventListener("input", function () {
        const v = this.value;

        document.getElementById("rule-capital")
            .classList.toggle("valid", /[A-Z]/.test(v));

        document.getElementById("rule-length")
            .classList.toggle("valid", v.length >= 8);

        document.getElementById("rule-number")
            .classList.toggle("valid", /[0-9]/.test(v));

        document.getElementById("rule-special")
            .classList.toggle("valid", /[_!@#$%]/.test(v));
    });
}

const forgotPass = document.getElementById("forgotNewPassword");

if (forgotPass) {
    forgotPass.addEventListener("input", function () {
        const v = this.value;

        document.getElementById("forgot-rule-capital")
            .classList.toggle("valid", /[A-Z]/.test(v));

        document.getElementById("forgot-rule-length")
            .classList.toggle("valid", v.length >= 8);

        document.getElementById("forgot-rule-number")
            .classList.toggle("valid", /[0-9]/.test(v));

        document.getElementById("forgot-rule-special")
            .classList.toggle("valid", /[_!@#$%]/.test(v));
    });
}
</script>