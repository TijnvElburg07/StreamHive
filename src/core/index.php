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

// Verzamel alle unieke categorieën over alle video's
$allCategories = [];
foreach ($videos as $v) {
    $cats = $videoClass->getVideoCategories($v['id']);
    foreach ($cats as $cat) {
        if (!in_array($cat, $allCategories)) {
            $allCategories[] = $cat;
        }
    }
}
sort($allCategories);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        <div class="search-filter-row">
            <input type="text" id="searchInput" placeholder="Zoek videos..." onkeyup="filterVideos()">

            <div class="filter-wrapper">
                <button class="filter-btn" id="filterToggle" onclick="toggleFilter()">
                    <i class="fa-solid fa-filter"></i>
                    Filter
                    <span class="filter-badge" id="filterBadge" style="display:none;">0</span>
                </button>

                <div class="filter-dropdown" id="filterDropdown">
                    <div class="filter-dropdown-header">
                        <span>Categorieën</span>
                        <button class="filter-clear" id="filterClear" onclick="clearFilters()" style="display:none;">
                            Wis alles
                        </button>
                    </div>
                    <div class="filter-options">
                        <?php foreach ($allCategories as $cat): ?>
                            <label class="filter-option">
                                <input
                                    type="checkbox"
                                    value="<?= htmlspecialchars(strtolower($cat)) ?>"
                                    onchange="filterVideos()"
                                >
                                <span class="filter-option-label"><?= htmlspecialchars($cat) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="videoGrid">
            <?php foreach ($videos as $v):
                $videoCat = $videoClass->getVideoCategories($v['id']);
                $catJson = htmlspecialchars(json_encode(array_map('strtolower', $videoCat)), ENT_QUOTES);
            ?>
                <div class="video-card"
                     data-title="<?= htmlspecialchars(strtolower($v['title'])) ?>"
                     data-description="<?= htmlspecialchars(strtolower($v['description'])) ?>"
                     data-categories='<?= $catJson ?>'>
                    <a href="pages/selectedVideo.php?id=<?= $v['id'] ?>">
                        <img src="../data/uploads/thumbnails/<?= htmlspecialchars($v['thumbnail']) ?>" alt="Thumbnail">
                        <p><?= htmlspecialchars($v['title']) ?></p>
                        <p><?= htmlspecialchars($v['description']) ?></p>
                        <p>Views: <?= $v['views'] ?></p>
                        <?php if (!empty($videoCat)): ?>
                            <div class="category-list">
                                <?php foreach ($videoCat as $cat): ?>
                                    <span class="category-tag"><?= htmlspecialchars($cat) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
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