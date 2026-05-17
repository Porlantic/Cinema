<?php
require_once 'config.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM movies WHERE movie_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

echo json_encode($stmt->get_result()->fetch_assoc());
exit;