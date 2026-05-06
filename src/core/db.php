<?php
include_once '../data/config.php';
// Create PDO connection

try {
    $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
