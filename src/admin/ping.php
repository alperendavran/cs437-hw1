<?php
// Admin oturum kontrolü
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['host'])) {
        // Kullanıcıdan gelen host girdisi
        $host = $_POST['host'];

        // Güvenlik açığı: Kullanıcı girdisini sanitize etmeden doğrudan shell komutuna dahil ediyoruz
        $output = shell_exec("ping -c 4 " . $host);

        // Çıktıyı göster
        echo "<h3>Ping Sonuçları:</h3>";
        echo "<pre>$output</pre>";
    }
} else {
    // Form ekranını göster
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Testi</title>
</head>
<body>
    <h1>Ping Testi</h1>
    <form method="post">
        <label for="host">Ping Yapılacak Hostname veya IP:</label><br>
        <input type="text" name="host" id="host" placeholder="Örnek: 8.8.8.8" required><br><br>
        <button type="submit">Ping Testi Yap</button>
    </form>
    <a href="dashboard.php">Geri Dön</a>
</body>
</html>
<?php
}
?>
