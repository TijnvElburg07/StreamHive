<?php
session_start();
include_once '../db.php';
require_once '../../methods/User.php';

$user = new User($pdo);

if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Haal alle beschikbare categorieën op voor het formulier
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$allCategories = $catStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $selectedCategories = $_POST['categories'] ?? []; // Array van gekozen category_id's

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

    // 1. Video opslaan
    $stmt = $pdo->prepare("INSERT INTO videos (user_id, title, description, filename, created_at, thumbnail) VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $description, $videoName, $thumbName]);
    
    // Pak de ID van de net geüploade video
    $videoId = $pdo->lastInsertId();

    // 2. Categorieën koppelen in de video_categories tabel
    if (!empty($selectedCategories) && $videoId) {
        $catInsert = $pdo->prepare("INSERT INTO video_categories (video_id, category_id) VALUES (?, ?)");
        foreach ($selectedCategories as $catId) {
            $catInsert->execute([$videoId, $catId]);
        }
    }

    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Upload Video</title>
</head>
<body>
    <main class="upload-page">
        <h1>Upload Video</h1>
        <div class="upload-card">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>

                <div class="field">
                    <label>Categories</label>
                    <div class="category-grid">
                        <?php foreach ($allCategories as $category): ?>
                            <label class="checkbox-container">
                                <input type="checkbox" name="categories[]" value="<?= $category['id'] ?>">
                                <span class="checkbox-label"><?= htmlspecialchars($category['name']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="field">
                    <label for="thumbnail">Thumbnail Image</label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                </div>

                <div class="field">
                    <label for="video">Video File</label>
                    <input type="file" id="video" name="video" accept="video/*" required>
                </div>

                <button type="submit" class="btn-primary">Start Upload</button>
            </form>
        </div>
    </main>
</body>
</html>