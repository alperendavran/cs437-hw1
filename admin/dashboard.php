<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

echo "<h1>Admin Dashboard</h1>";
echo "<a href='manage_news.php'>Manage News</a><br>";
echo "<a href='../index.php'>Back to Site</a>";
?>
