<?php
session_start();

// 1. Inclusief database en de benodigde class
include_once '../db.php';
require_once '../../methods/User.php';
require_once '../../methods/Comments.php';

$user = new User($pdo);
$commentsClass = new Comments($pdo);

if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $videoId = isset($_POST['video_id']) ? (int)$_POST['video_id'] : 0;
    $commentText = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $userId = $_SESSION['user_id'];

    if ($videoId > 0 && !empty($commentText)) {
        
        // Voeg de comment toe via de class methode
        $success = $commentsClass->addComment($userId, $videoId, $commentText);

        if ($success) {
            // Succes! Stuur terug naar de video pagina
            header("Location: selectedVideo.php?id=$videoId");
            exit();
        } else {
            die("Er is iets misgegaan bij het opslaan van je reactie.");
        }
        
    } else {
        header("Location: selectedVideo.php?id=$videoId");
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}