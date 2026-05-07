<?php
class Comments {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getCommentsByVideoId($videoId) {
        $stmt = $this->pdo->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE video_id = ? ORDER BY created_at DESC");
        $stmt->execute([$videoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($userId, $videoId, $commentText) {
        $stmt = $this->pdo->prepare("INSERT INTO comments (user_id, video_id, content, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$userId, $videoId, $commentText]);
    }

    public function editComment($commentId, $userId, $newText) {
        $stmt = $this->pdo->prepare("UPDATE comments SET content = ? WHERE id = ? AND user_id = ?");
        return $stmt->execute([$newText, $commentId, $userId]);
    }

    public function getAuthor($commentId) {
        $stmt = $this->pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$commentId]);
        return $stmt->fetchColumn();
    }

    public function deleteComment($commentId, $userId) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        return $stmt->execute([$commentId, $userId]);
    }
}
?>