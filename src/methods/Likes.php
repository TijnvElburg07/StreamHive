<?php
include_once __DIR__ .  '/../core/db.php';

class Likes{
    private $pdo;
    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    // Method to check if a user has liked a video
    public function hasLiked($userId, $videoId) {
        $stmt = $this->pdo->prepare("SELECT * FROM likes WHERE user_id = :user_id AND video_id = :video_id");
        $stmt->execute(['user_id' => $userId, 'video_id' => $videoId]);
        return $stmt->fetch() !== false;
    }

    // Method to like a video
    public function likeVideo($userId, $videoId) {
        if ($this->hasLiked($userId, $videoId)) {
            return;
        }
        $stmt = $this->pdo->prepare("INSERT INTO likes (user_id, video_id) VALUES (:user_id, :video_id)");
        $stmt->execute(['user_id' => $userId, 'video_id' => $videoId]);
    }

    // Method to unlike a video
    public function unlikeVideo($userId, $videoId) {
        if (!$this->hasLiked($userId, $videoId)) {
            return;
        }
        $stmt = $this->pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND video_id = :video_id");
        $stmt->execute(['user_id' => $userId, 'video_id' => $videoId]);
    }

    // Method to get the total number of likes for a video
    public function getLikesCount($videoId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE video_id = :video_id");
        $stmt->execute(['video_id' => $videoId]);
        return $stmt->fetchColumn();
    }
}