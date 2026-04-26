<?php
require_once 'config.php';
header('Content-Type: application/json');

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$price = str_replace(',', '', $_POST['price'] ?? '');
$status = $_POST['status'] ?? 'now-showing';

$date = $_POST['date'] ?? '';
$time = $_POST['time'] ?? '';
$id = $_POST['movie_id'] ?? null;

if ($status !== 'now-showing' && $status !== 'scheduled') {
    $status = 'now-showing';
}

$datetime = ($date && $time) ? "$date $time:00" : null;

$poster = '';

if (!empty($_FILES['poster']['name'])) {
    $dir = "uploads/";
    if (!file_exists($dir)) mkdir($dir);

    $poster = $dir . time() . "_" . basename($_FILES['poster']['name']);
    move_uploaded_file($_FILES['poster']['tmp_name'], $poster);
}

if ($id) {

    if ($poster) {
        $stmt = $conn->prepare("UPDATE movies SET title=?,description=?,price=?,status=?,datetime=?,poster=? WHERE movie_id=?");
        $stmt->bind_param("ssdsssi", $title,$description,$price,$status,$datetime,$poster,$id);
    } else {
        $stmt = $conn->prepare("UPDATE movies SET title=?,description=?,price=?,status=?,datetime=? WHERE movie_id=?");
        $stmt->bind_param("ssdssi", $title,$description,$price,$status,$datetime,$id);
    }

} else {

    $stmt = $conn->prepare("INSERT INTO movies (title,description,price,status,datetime,poster) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssdsss", $title,$description,$price,$status,$datetime,$poster);
}

echo json_encode(["success"=>$stmt->execute()]);
exit;