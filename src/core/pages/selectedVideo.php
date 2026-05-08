<?php
session_start();

include_once '../db.php';
require_once '../../methods/User.php';
$user = new User($pdo);
require_once '../../methods/Video.php';
$videoClass = new Video($pdo);
require_once '../../methods/Likes.php';
$likes = new Likes($pdo);
require_once '../../methods/Comments.php';
$commentsClass = new Comments($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ../index.php');
    exit;
}

// Haal video details op
$stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
$stmt->execute([$id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    header('Location: ../index.php');
    exit;
}

$videoClass->addView($id);

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

if ($loggedIn && isset($_POST['delete_comment'])) {
    $commentIdToDelete = (int)$_POST['comment_id'];
    $commentsClass->deleteComment($commentIdToDelete, $userId);
    header("Location: ?id=$id");
    exit;
}

$likeCount = $likes->getLikesCount($id);
$userLiked = $loggedIn && $likes->hasLiked($userId, $id);

$allComments = $commentsClass->getCommentsByVideoId($id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <title><?= htmlspecialchars($video['title']) ?> - VideoSite</title>
</head>
<body>

    <nav>
        <span>VideoSite</span>
        <a href="../index.php"><button>Home</button></a>
    </nav>

    <main class="video-page">
        <a href="../index.php" class="back-link">← Terug naar overzicht</a>

        <h1><?= htmlspecialchars($video['title']) ?></h1>
        <p><?= (int)$video['views'] ?> weergaven</p>

        <video controls poster="../../data/uploads/thumbnails/<?= htmlspecialchars($video['thumbnail']) ?>">
            <source src="../../data/uploads/videos/<?= htmlspecialchars($video['filename']) ?>" type="video/mp4">
            Je browser ondersteunt deze video niet.
        </video>

        <div class="video-info">
            <div class="video-actions">
                <?php if ($loggedIn): ?>
                    <form method="POST">
                        <button type="submit" name="toggle_like">
                            <?= $userLiked ? '<i class="fa-solid fa-thumbs-up"></i>' : '<i class="fa-regular fa-thumbs-up"></i>' ?>
                            <span><?= $likeCount ?></span>
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn-ghost" onclick="window.location.href='login.php'">
                        <i class="fa-regular fa-thumbs-up"></i> <span><?= $likeCount ?></span>
                    </button>
                <?php endif; ?>
            </div>

            <div class="video-description">
                <p><?= nl2br(htmlspecialchars($video['description'])) ?></p>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border); margin: 2rem 0;">

        <section class="comment-section">
            <h3><?= count($allComments) ?> Reacties</h3>
            
            <?php if ($loggedIn): ?>
                <form action="postComment.php" method="POST" class="comment-form">
                    <input type="hidden" name="video_id" value="<?= $id ?>">
                    <textarea name="comment" placeholder="Voeg een reactie toe..." required></textarea>
                    <button type="submit" class="btn-primary">Reageren</button>
                </form>
            <?php else: ?>
                <p style="color: var(--text-soft); font-size: 0.9rem; margin-bottom: 2rem;">
                    <a href="login.php" style="color: var(--blue-bright);">Log in</a> om te reageren.
                </p>
            <?php endif; ?>

            <div class="comment-list">
                <?php if (empty($allComments)): ?>
                    <p style="color: var(--muted); font-style: italic;">Nog geen reacties. Wees de eerste!</p>
                <?php else: ?>
                    <?php foreach ($allComments as $c): ?>
                        <div class="comment-item">
                            <div class="comment-avatar"></div>
                            <div class="comment-content">
                                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                    <span class="comment-author"><?= htmlspecialchars($c['username']) ?></span>
                                    
                                    <?php if ($loggedIn && (int)$c['user_id'] === (int)$userId): ?>
                                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je deze reactie wilt verwijderen?');">
                                            <input type="hidden" name="comment_id" value="<?= $c['id'] ?>">
                                            <button type="submit" name="delete_comment" style="background:none; border:none; color:var(--danger); cursor:pointer; font-size: 0.75rem;">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <p class="comment-text"><?= nl2br(htmlspecialchars($c['content'])) ?></p>
                                <span style="font-size: 0.7rem; color: var(--muted);"><?= date('d-m-Y H:i', strtotime($c['created_at'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

</body>
</html>