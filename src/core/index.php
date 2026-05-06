

<?php

// include db connection
$pdo = require_once 'db.php';
require_once '../methods/Video.php';

// print all videos
$video = new Video($pdo);
$videos = $video->getAllVideos();
foreach ($videos as $v) {
    echo $v['title'] . " - " . $v['description'] . "<br>";
}

// {# -- checks login status and redirects to login page if not logged in #}

// {# -- Start session #}

// {# -- homepage HTML content #}

// {# -- connect JS and CSS to HTML #}