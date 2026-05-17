<?php
require_once 'config.php';
header('Content-Type: application/json');

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$price = str_replace(',', '', $_POST['price'] ?? '');
$status = $_POST['status'] ?? 'now-showing';

$show_date = $_POST['date'] ?? null;
$show_time = $_POST['time'] ?? null;
$id = $_POST['movie_id'] ?? null;

/* 🔥 NEW: GENRES */
$genres = $_POST['genres'] ?? ''; // "1,2,3"

if ($status !== 'now-showing' && $status !== 'scheduled') {
    $status = 'now-showing';
}

if (empty($show_date)) $show_date = null;
if (empty($show_time)) $show_time = null;

$poster = '';

if (!empty($_FILES['poster']['name'])) {
    $dir = "uploads/";
    if (!file_exists($dir)) mkdir($dir);

    $poster = $dir . time() . "_" . basename($_FILES['poster']['name']);
    move_uploaded_file($_FILES['poster']['tmp_name'], $poster);
}

/* =========================
   INSERT OR UPDATE MOVIE
========================= */

if ($id) {

    if ($poster) {
        $stmt = $conn->prepare("UPDATE movies SET title=?,description=?,price=?,status=?,show_date=?,show_time=?,poster=? WHERE movie_id=?");
        $stmt->bind_param("ssdssssi", $title,$description,$price,$status,$show_date,$show_time,$poster,$id);
    } else {
        $stmt = $conn->prepare("UPDATE movies SET title=?,description=?,price=?,status=?,show_date=?,show_time=? WHERE movie_id=?");
        $stmt->bind_param("ssdsssi", $title,$description,$price,$status,$show_date,$show_time,$id);
    }

    $success = $stmt->execute();

    /* 🔥 DELETE OLD GENRES */
    $conn->query("DELETE FROM movie_genres WHERE movie_id = $id");

} else {

    $stmt = $conn->prepare("INSERT INTO movies (title,description,price,status,show_date,show_time,poster) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("ssdssss", $title,$description,$price,$status,$show_date,$show_time,$poster);

    $success = $stmt->execute();

    $id = $conn->insert_id; // 🔥 GET NEW MOVIE ID
}

/* =========================
   SAVE GENRES
========================= */

if ($success && !empty($genres)) {

    $genreArray = explode(",", $genres);

    foreach ($genreArray as $g) {
        $g = intval($g);
        if ($g > 0) {
            $conn->query("INSERT INTO movie_genres (movie_id, genre_id) VALUES ($id, $g)");
        }
    }
}

echo json_encode(["success"=>$success]);
exit;