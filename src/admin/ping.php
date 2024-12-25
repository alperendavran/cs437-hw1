<?php
// Admin session control
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['host'])) {
        // Input from user (potentially malicious)
        $host = $_POST['host'];

        // Check if the URL format is correct, add "http://" if missing
        if (!preg_match('/^http(s)?:\/\//', $host)) {
            $host = "http://$host";
        }

        /*
        VULNERABILITY:
        The following shell_exec command directly takes user input ($host) without proper sanitization.
        This allows an attacker to inject malicious OS commands by appending them to the input.
        For example:
        - Input: "www.example.com; sleep 30"
          This will execute the command: "curl -I -m 10 www.example.com; sleep 30"
          As a result, the system will pause for 30 seconds, demonstrating Blind OS Command Injection.
        */
        $output = shell_exec("curl -I -m 10 $host 2>&1");

        // Analyzing the output to determine the result
        echo "<h3>Sonuç:</h3>";
        if (strpos($output, '200 OK') !== false || strpos($output, '301 Moved Permanently') !== false) {
            echo "<p>URL kullanılabilir: $host</p>";
        } else {
            echo "<p>URL kullanılamıyor: $host</p>";
        }

        // Provide a link to return to the main page
        echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">URL Kontrol Sayfasına Geri Dön</a>';
    }
} else {
    // Display the form for URL input
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Kontrol</title>
</head>
<body>
    <h1>URL Kontrol</h1>
    <form method="post">
        <label for="host">Kontrol edilecek URL'yi girin:</label><br>
        <input type="text" name="host" id="host" placeholder="Örnek: www.example.com" required><br><br>
        <button type="submit">Kontrol Et</button>
    </form>
    <a href="dashboard.php">Admin Paneline Geri Dön</a>

    <hr>
    <h3>Haber Kaynaklarımız:</h3>
    <ul>
        <li><a href="https://www.haberturk.com" target="_blank">www.haberturk.com</a></li>
        <li><a href="https://www.cnnturk.com" target="_blank">www.cnnturk.com</a></li>
    </ul>
</body>
</html>
<?php
}
?>
