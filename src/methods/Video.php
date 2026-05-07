
<?php
include_once __DIR__ .  '/../core/db.php';

class Video{

    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAllVideos(){
        $stmt = $this->pdo->prepare("SELECT * FROM videos ORDER BY views DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVideoCreator($videoId){
        $stmt = $this->pdo->prepare("SELECT users.username FROM videos JOIN users ON videos.user_id = users.id WHERE videos.id = ?");
        $stmt->execute([$videoId]);
        return $stmt->fetchColumn();
    }

    public function addView($videoId){
        if (!isset($_SESSION['viewed_videos'])) {
            $_SESSION['viewed_videos'] = [];
        }


        if (!in_array($videoId, $_SESSION['viewed_videos'])) {
            $stmt = $this->pdo->prepare("UPDATE videos SET views = views + 1 WHERE id = ?");
            $stmt->execute([$videoId]);

            $_SESSION['viewed_videos'][] = $videoId;
        }
    }

    public function deleteVideo($videoId){
        $stmt = $this->pdo->prepare("DELETE FROM videos WHERE id = ?");
        $stmt->execute([$videoId]);
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE video_id = ?");
        $stmt->execute([$videoId]);
        $stmt = $this->pdo->prepare("DELETE FROM likes WHERE video_id = ?");
        $stmt->execute([$videoId]);
        $stmt = $this->pdo->prepare("DELETE FROM video_category WHERE video_id = ?");
        $stmt->execute([$videoId]);

        // Verwijder het videobestand en de thumbnail van de server
        $stmt = $this->pdo->prepare("SELECT filename, thumbnail FROM videos WHERE id = ?");
        $stmt->execute([$videoId]);
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($video) {
            $videoPath = __DIR__ . '/../data/uploads/videos/' . $video['filename'];
            $thumbPath = __DIR__ . '/../data/uploads/thumbnails/' . $video['thumbnail'];

            if (file_exists($videoPath)) {
                unlink($videoPath);
            }
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
    }

    public function getVideoCategories($videoId){
        $stmt = $this->pdo->prepare("SELECT category_id FROM video_category WHERE video_id = ?");
        $stmt->execute([$videoId]);
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $stmt = $this->pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$result[0]]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}