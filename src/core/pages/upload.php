<?php
session_start();
include_once '../db.php';
require_once '../../methods/User.php';

$user = new User($pdo);

if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    $videoDir = '../../data/uploads/videos/';
    $thumbDir = '../../data/uploads/thumbnails/';

    if (!isset($_FILES['video']) || $_FILES['video']['error'] !== 0) {
        die("Video upload mislukt");
    }

    $videoExt = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
    $videoName = uniqid('video_', true) . '.' . $videoExt;
    $videoPath = $videoDir . $videoName;

    move_uploaded_file($_FILES['video']['tmp_name'], $videoPath);

    $thumbName = null;

    if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === 0) {

        $thumbExt = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        $thumbName = uniqid('thumb_', true) . '.' . $thumbExt;
        $thumbPath = $thumbDir . $thumbName;

        move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbPath);
    }

    $stmt = $pdo->prepare("INSERT INTO videos (user_id, title, description, filename, created_at, thumbnail) VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $description, $videoName, $thumbName]);

    echo "Upload succesvol!";
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Video</title>
</head>
<body>
    <h1>Upload Video</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br><br>

        <label for="thumbnail">Thumbnail:</label>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*"><br><br>

        <label for="video">Video File:</label>
        <input type="file" id="video" name="video" accept="video/*" required><br><br>

        <button type="submit">Upload</button>
    </form>
</body>
</html>