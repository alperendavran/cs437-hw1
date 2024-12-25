<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Varsayılan dosya yolu
$base_dir = './uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    // Formdan gelen dosya adını al ve URL'ye yönlendir
    $filename = $_POST['filename'];
    header("Location: readfile.php?file=" . urlencode($filename));
    exit;
}

if (isset($_GET['file'])) {
    // URL'den gelen dosya adını al ve dosya yolunu oluştur
    $filename = $_GET['file'];
    $file_path = $base_dir . $filename;

    // Dosyanın varlığını kontrol et ve içeriği oku
    if (file_exists($file_path)) {
        $content = file_get_contents($file_path);
        echo "<h3>Dosya İçeriği:</h3>";
        echo "<pre>$content</pre>";
    } else {
        echo "<h3>Dosya Bulunamadı</h3>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosya Okuma Özelliği</title>
</head>
<body>
    <h1>Dosya Okuma Özelliği</h1>
    <form method="post">
        <label for="filename">Dosya Adı:</label><br>
        <input type="text" name="filename" id="filename" placeholder="test.txt" required><br><br>
        <button type="submit">Dosyayı Oku</button>
    </form>
    <a href="dashboard.php">Admin Dashboard'a Geri Dön</a>
</body>
</html>