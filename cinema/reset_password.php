<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

// Validate password rules
if (strlen($password) < 8) {
    echo json_encode(["success" => false, "error" => "Password must be at least 8 characters"]);
    exit;
}
if (!preg_match('/[A-Z]/', $password)) {
    echo json_encode(["success" => false, "error" => "Password must contain at least one capital letter"]);
    exit;
}
if (!preg_match('/[0-9]/', $password)) {
    echo json_encode(["success" => false, "error" => "Password must contain at least one number"]);
    exit;
}
if (!preg_match('/[_!@#$%]/', $password)) {
    echo json_encode(["success" => false, "error" => "Password must contain a special character"]);
    exit;
}

// Verify email exists
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "Email not found"]);
    exit;
}

// Update password
$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->bind_param("ss", $hashed, $email);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to reset password"]);
}
?>
