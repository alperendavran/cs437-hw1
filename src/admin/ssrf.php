<?php
session_start();

// Sadece admin kullanıcılar bu sayfaya erişebilsin:
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url']);
    
    // Basit bir blacklist listesi (hiçbir zaman %100 güvenli değil)
    $blacklist = [
        '127.0.0.1',
        'localhost',
        '169.254',     // AWS/ECS metadata gibi dahili IP’ler
        '::1',         // IPv6 localhost
    ];

    // Eğer URL içinde bu blacklisted ifadelere rastlarsak yasaklayalım
    foreach ($blacklist as $blocked) {
        if (strpos($url, $blocked) !== false) {
            die("<p style='color:red;'>ERROR: This URL is blocked by our blacklist policy!</p>");
        }
    }

    // SSRF isteği: file_get_contents, cURL vs. kullanabilirsiniz
    // Burada basitlik için file_get_contents örneği gösteriyoruz
    $response = @file_get_contents($url);

    if ($response === false) {
        echo "<p style='color:red;'>Failed to fetch content from the given URL.</p>";
    } else {
        // DİKKAT: Yanıtı doğrudan ekrana basmak da XSS veya benzeri riskler taşıyabilir
        echo "<h3>Fetched Content:</h3>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSRF Example (Blacklist-based Filter)</title>
</head>
<body>
    <h1>SSRF Demo (Blacklist-based Filter)</h1>
    <p>Bu sayfada, girdiğiniz URL'e sunucu üzerinden istek atıp yanıtı görüntüleyen basit bir form bulunmaktadır.</p>
    <p><strong>Uyarı:</strong> Sadece demonstrasyon amaçlı bir koddur ve çeşitli bypass yöntemlerine karşı savunmasızdır.</p>
    
    <form method="POST">
        <label for="url">URL to Fetch:</label><br>
        <input type="text" name="url" id="url" size="50" required>
        <br><br>
        <button type="submit">Send Request</button>
    </form>
</body>
</html>