<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Default file directory
$base_dir = './uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    // Retrieve the filename from the form and redirect to readfile.php
    $filename = $_POST['filename'];

    /*
    VULNERABILITY:
    The filename provided by the user ($_POST['filename']) is directly appended to the URL
    without validation or sanitization.
    This exposes the user's input in the URL, which can be exploited by an attacker
    to perform Path Traversal attacks.

    For example:
    - Input: ../../supersecret/gizli_darbe_plani.txt
    - Redirected URL: readfile.php?file=../../supersecret/gizli_darbe_plani.txt
    */
    header("Location: readfile.php?file=" . urlencode($filename));
    exit;
}

if (isset($_GET['file'])) {
    // Retrieve the filename from the URL and construct the file path
    $filename = $_GET['file'];

    /*
    VULNERABILITY:
    The user-provided filename ($_GET['file']) is directly concatenated with the base directory ($base_dir).
    This allows attackers to manipulate the file path using "../" sequences to access files outside
    the intended directory.

    Additionally:
    - The user input is visible in the URL (e.g., ?file=..%2F..%2Fsupersecret%2Fgizli_darbe_plani.txt),
      providing direct feedback to the attacker about the effectiveness of their input.
    */
    $file_path = $base_dir . $filename;

    // Check if the file exists and read its content
    if (file_exists($file_path)) {
        /*
        VULNERABILITY:
        If an attacker successfully navigates to a sensitive file using a Path Traversal attack,
        the content of the file is displayed directly to the attacker, further exposing sensitive information.
        */
        $content = file_get_contents($file_path);
        echo "<h3>Dosya İçeriği:</h3>";
        echo "<pre>$content</pre>";
    } else {
        echo "<h3>Dosya Bulunamadı</h3>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosya Okuma Özelliği</title>
</head>
<body>
    <h1>Dosya Okuma Özelliği</h1>
    <p>Bu özellik, admin tarafından yüklenen dosyaların görüntülenmesini sağlar.</p>
    <form method="post">
        <label for="filename">Dosya Adı:</label><br>
        <input type="text" name="filename" id="filename" placeholder="test.txt" required><br><br>
        <button type="submit">Dosyayı Oku</button>
    </form>
    <a href="dashboard.php">Admin Paneline Geri Dön</a>
</body>
</html>
