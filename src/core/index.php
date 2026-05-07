<?php
session_start();
$pdo = require_once 'db.php';
require_once '../methods/Video.php';
require_once '../methods/User.php';

$videoClass = new Video($pdo);
$user = new User($pdo);
$videos = $videoClass->getAllVideos();
$isLoggedIn = $user->isLoggedIn();
$currentUsername = $isLoggedIn ? $user->getUsername($user->getUserId()) : null;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>VideoSite</title>
</head>
<body>

    <nav>
        <span>VideoSite</span>
        <?php if ($isLoggedIn): ?>
            <a href="pages/upload.php"><button>Upload Video</button></a>
            <a href="pages/logout.php"><button>Logout</button></a>
        <?php else: ?>
            <a href="pages/login.php"><button>Login</button></a>
        <?php endif; ?>
    </nav>

    <main>
        <h1>Videos</h1>

        <input type="text" id="searchInput" placeholder="Zoek videos..." onkeyup="filterVideos()">

        <div id="videoGrid">
            <?php foreach ($videos as $v): ?>
                <div class="video-card" data-title="<?= htmlspecialchars(strtolower($v['title'])) ?>">
                    <a href="pages/selectedVideo.php?id=<?= $v['id'] ?>">
                        <img src="../data/uploads/thumbnails/<?= htmlspecialchars($v['thumbnail']) ?>" alt="Thumbnail">
                        <p><?= htmlspecialchars($v['title']) ?></p>
                        <p><?= htmlspecialchars($v['description']) ?></p>
                        <p>Views: <?= $v['views'] ?></p>
                    </a>
                    <?php if ($currentUsername && $videoClass->getVideoCreator($v['id']) === $currentUsername): ?>
                        <a href="pages/deleteVideo.php?id=<?= $v['id'] ?>"><button>Delete</button></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <p id="noResults" style="display:none;">Geen videos gevonden.</p>
    </main>

    <script src="js/script.js"></script>

</body>
</html>