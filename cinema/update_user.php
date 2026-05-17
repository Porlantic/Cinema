<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['name']) || !isset($data['email'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$user_id = (int) $data['user_id'];
$name = trim($data['name']);
$email = trim($data['email']);
$password = trim($data['password'] ?? '');

if (empty($name) || empty($email)) {
    echo json_encode(["success" => false, "error" => "Name and email cannot be empty"]);
    exit;
}

// Update name and email
if (!empty($password)) {
    // Update name, email, and password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $name, $email, $hashedPassword, $user_id);
} else {
    // Update name and email only
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
}

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Database update failed"]);
}
?>
