<?php
session_start();
require_once '../../methods/Video.php';
$videoClass = new Video($pdo);

$videoClass->deleteVideo((int)$_GET['id']);