<?php
include_once '../db.php';
require_once '../../methods/User.php';
$user = new User($pdo);
require_once '../../methods/Video.php';
$videoClass = new Video($pdo);

// Haal het video ID op uit de URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../index.php');
    exit;
}

// Haal de video op uit de database
$stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
$stmt->execute([$id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

// Als de video niet bestaat, redirect terug
if (!$video) {
    header('Location: ../index.php');
    exit;
}

// Verhoog het view-aantal
$videoClass->addView($id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($video['title']) ?></title>
</head>
<body>

    <h1><?= htmlspecialchars($video['title']) ?></h1>
    <p><?= htmlspecialchars($video['description']) ?></p>
    <p>Views: <?= (int)$video['views'] ?></p>

    <video width="640" height="480" controls>
        <source src="../../data/uploads/videos/<?= htmlspecialchars($video['filename']) ?>" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <br><br>
    <a href="../index.php">← Terug naar overzicht</a>

</body>
</html>