<?php
require_once '../../methods/User.php';
$user = new User($pdo);

$user->logout();