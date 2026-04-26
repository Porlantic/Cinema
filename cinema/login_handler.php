<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Get inputs
$email = $_POST['login_email'] ?? '';
$password = $_POST['login_password'] ?? '';

// Validate
if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Email and password are required."
    ]);
    exit();
}

// Query user
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Database error"
    ]);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check user
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // ✅ Plain text password check (prototype only)
    if (password_verify($password, $user['password'])) {

        // ✅ SET ALL REQUIRED SESSIONS
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];   // 🔥 IMPORTANT FIX
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];

        // ✅ FIXED REDIRECTS
        if ($user['role'] === 'admin') {
            $redirect = "admin.php";
        } else {
            $redirect = "user_dashboard.php"; // 🔥 FIXED (was user.php)
        }

        echo json_encode([
            "success" => true,
            "redirect" => $redirect
        ]);
        exit();

    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid credentials"
        ]);
        exit();
    }

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials"
    ]);
    exit();
}