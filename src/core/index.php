

<?php
session_start();
// include db connection
$pdo = require_once 'db.php';
require_once '../methods/Video.php';
require_once '../methods/User.php';
$videoClass = new Video($pdo);
$user = new User($pdo);

$video = new Video($pdo);
$videos = $video->getAllVideos();
foreach ($videos as $v) {
    echo $v['title'] . " - " . $v['description'] . " - Views: " . $v['views'] . "<br>";
    // clickable thumbnail that links to video page
    echo '<a href="pages/selectedVideo.php?id=' . $v['id'] . '">';
    echo '<img src="../data/uploads/thumbnails/' . $v['thumbnail'] . '" alt="Thumbnail"><br>';
    if ($videoClass->getVideoCreator($v['id']) == $user->getUsername($user->getUserId())) {
        // add delete button that links to delete video page
        echo '<a href="pages/deleteVideo.php?id=' . $v['id'] . '"><button>Delete</button></a>';
    }
    // echo '<video width="320" height="240" controls><source src="../data/uploads/videos/' . $v['filename'] . '" type="video/mp4"></video><br>';
}

// {# -- checks login status and redirects to login page if not logged in #}
$user = new User($pdo);
if (!$user->isLoggedIn()) {
    // create button for login page
    echo '<a href="pages/login.php"><button>Login</button></a>';
} else {
    echo '<a href="pages/logout.php"><button>Logout</button></a>';
    echo '<a href="pages/upload.php"><button>Upload Video</button></a>';
}

// {# -- Start session #}

// {# -- homepage HTML content #}

// {# -- connect JS and CSS to HTML #}