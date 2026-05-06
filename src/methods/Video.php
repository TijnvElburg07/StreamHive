
<?php
include_once '../core/db.php';

class Video{

    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAllVideos(){
        $stmt = $this->pdo->prepare("SELECT * FROM videos ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // {# Video class to handle video-related operations #}

    // {# Method to upload a video #}

    // {# Method to delete a video #}

    // {# Method to get all videos #}

    // {# Method to get a video by ID #}

    // {# Method to get all videos uploaded by a specific user #}

    // {# Method to search for videos by title or description #}

    // {# Method to increment the view count of a video #}
}