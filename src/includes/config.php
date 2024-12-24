<?php
$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'mydatabase';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'secret';
$port = '3306'; // inside Docker

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass);
    //echo "Connected!";
} catch (PDOException $e) {
    //echo "Connection failed: " . $e->getMessage();
}
