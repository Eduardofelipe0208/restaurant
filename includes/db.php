<?php
session_start();
$host = '127.0.0.1'; $dbname = 'restaurant_menu';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
                   $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASS'] ?? '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { die('DB Connection Error: ' . $e->getMessage()); }
?>
