<?php
session_start();

include_once '../db.php';
require_once '../../methods/User.php';
$user = new User($pdo);
require_once '../../methods/Video.php';
$videoClass = new Video($pdo);
require_once '../../methods/Likes.php';
$likes = new Likes($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
$stmt->execute([$id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    header('Location: ../index.php');
    exit;
}

$videoClass->addView($id);

// --- Like logic ---
$loggedIn = isset($_SESSION['user_id']);
$userId = $loggedIn ? $_SESSION['user_id'] : null;

if ($loggedIn && isset($_POST['toggle_like'])) {
    if ($likes->hasLiked($userId, $id)) {
        $likes->unlikeVideo($userId, $id);
    } else {
        $likes->likeVideo($userId, $id);
    }
    header("Location: ?id=$id");
    exit;
}

$likeCount  = $likes->getLikesCount($id);
$userLiked  = $loggedIn && $likes->hasLiked($userId, $id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

    <?php if ($loggedIn): ?>
        <form method="POST">
            <button type="submit" name="toggle_like">
                <?= $userLiked ? '<i class="fa-solid fa-thumbs-up"></i>' : '<i class="fa-regular fa-thumbs-up"></i>' ?>
            </button>
            <span><?= $likeCount ?> like<?= $likeCount !== 1 ? 's' : '' ?></span>
        </form>
    <?php else: ?>
        <p>🤍 <?= $likeCount ?> like<?= $likeCount !== 1 ? 's' : '' ?> — <a href="../login.php">Log in to like</a></p>
    <?php endif; ?>

    <br><br>
    <a href="../index.php">← Terug naar overzicht</a>

</body>
</html>