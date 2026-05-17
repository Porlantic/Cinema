<?php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_GET['title'])) {
    echo json_encode(['success' => false, 'error' => 'Movie title is required']);
    exit;
}

$title = $_GET['title'];

// Get movie ID from title
$stmt = $conn->prepare("SELECT movie_id FROM movies WHERE title = ?");
$stmt->bind_param("s", $title);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Movie not found']);
    exit;
}

$movie = $result->fetch_assoc();
$movie_id = $movie['movie_id'];

// Get genres for this movie
$stmt = $conn->prepare("
    SELECT g.name 
    FROM genres g 
    JOIN movie_genres mg ON g.genre_id = mg.genre_id 
    WHERE mg.movie_id = ?
    ORDER BY g.name
");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

$genres = [];
while ($row = $result->fetch_assoc()) {
    $genres[] = $row['name'];
}

if (empty($genres)) {
    echo json_encode(['success' => true, 'genres' => 'No genres assigned']);
} else {
    echo json_encode(['success' => true, 'genres' => implode(', ', $genres)]);
}
?>
