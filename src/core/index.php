

<?php
// include db connection
$pdo = require_once 'db.php';
require_once '../methods/Video.php';
require_once '../methods/User.php';

// print all videos
$video = new Video($pdo);
$videos = $video->getAllVideos();
foreach ($videos as $v) {
    echo $v['title'] . " - " . $v['description'] . "<br>";
}

// {# -- checks login status and redirects to login page if not logged in #}
$user = new User($pdo);
if (!$user->isLoggedIn()) {
    // create button for login page
    echo '<a href="pages/login.php"><button>Login</button></a>';
}

// {# -- Start session #}

// {# -- homepage HTML content #}

// {# -- connect JS and CSS to HTML #}