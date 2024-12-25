<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <ul>
        <li><a href="manage_news.php">Manage News</a></li>
        <li><a href="ping.php">Ping Test</a></li>
        <li><a href="readfile.php">Güvenli Dosya Okuma</a></li>
        <li><a href="../index.php">Back to Site</a></li>
    </ul>
</body>
</html>
