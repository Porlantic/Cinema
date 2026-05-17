<?php
require_once 'config.php';
header('Content-Type: application/json');

// GET ALL GENRES
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM genres ORDER BY name ASC");
    $genres = [];

    while ($row = $result->fetch_assoc()) {
        $genres[] = $row;
    }

    echo json_encode($genres);
    exit;
}

// ADD NEW GENRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        echo json_encode(['success' => false, 'error' => 'Genre name is required']);
        exit;
    }

    $check = $conn->prepare("SELECT genre_id FROM genres WHERE name = ?");
    $check->bind_param("s", $name);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Genre already exists']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO genres (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'genre_id' => $stmt->insert_id,
            'name' => $name
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add genre']);
    }

    exit;
}

// DELETE GENRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $genre_id = (int)($_POST['genre_id'] ?? 0);

    if ($genre_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid genre ID']);
        exit;
    }

    $check = $conn->prepare("SELECT COUNT(*) AS count FROM movie_genres WHERE genre_id = ?");
    $check->bind_param("i", $genre_id);
    $check->execute();

    $result = $check->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        echo json_encode(['success' => false, 'error' => 'Cannot delete genre - it is being used by movies']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM genres WHERE genre_id = ?");
    $stmt->bind_param("i", $genre_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete genre']);
    }

    exit;
}