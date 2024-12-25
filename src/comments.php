<?php
// Include database connection
include 'includes/config.php';

/**
 * BLACK LİST DOMAİNS
 */
$blacklist = [
    'example.com',
    'malicious-site.net',
    'banned-keyword',
    '127.0.0.1',
    'localhost',
    '0.0.0.0',
];

/**
 * CHECHK BLACK LİST
 */
function isBlacklisted($link, $blacklist) {
    foreach ($blacklist as $blocked) {
        if (stripos($link, $blocked) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * FROM LİNKS BASED HTML
 */
function generatePreviewHtml($link) {
    global $blacklist; // BLACK LİST CHECK AS GLOABLA 

    // BLACK LİST CHECK
    if (isBlacklisted($link, $blacklist)) {
        die("Error: The provided link is not allowed.");
    }

    // 1) YouTube kontrolü
    if (strpos($link, 'youtube.com') !== false || strpos($link, 'youtu.be') !== false) {
        if (preg_match('/v=([^&]+)/', $link, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/youtu\.be\/([^?]+)/', $link, $matches)) {
            $videoId = $matches[1];
        } else {
            return '<a href="' . htmlspecialchars($link) . '" target="_blank">'
                   . htmlspecialchars($link) . '</a>';
        }
        return '
            <iframe width="560" height="315"
                src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '"
                frameborder="0" allowfullscreen>
            </iframe>
        ';
    }

    // 2) Twitter kontrolü
    if (strpos($link, 'twitter.com') !== false) {
        return '
            <blockquote class="twitter-tweet">
                <a href="' . htmlspecialchars($link) . '">View Tweet</a>
            </blockquote>
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        ';
    }

    // 3) Diğer linkler
    return '<a href="' . htmlspecialchars($link) . '" target="_blank">'
           . htmlspecialchars($link) . '</a>';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $university_name = $_POST['university_name'];
    $commenter_name  = $_POST['commenter_name'];
    $comment         = $_POST['comment'];
    $link           = isset($_POST['link']) ? trim($_POST['link']) : '';

    // Preview HTML oluştur
    $previewHtml = '';
    if (!empty($link)) {
        $previewHtml = generatePreviewHtml($link);
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO university_comments (university_name, commenter_name, comment, link, preview_html) 
            VALUES (:university_name, :commenter_name, :comment, :link, :preview_html)
        ");
        $stmt->execute([
            ':university_name' => $university_name,
            ':commenter_name'  => $commenter_name,
            ':comment'         => $comment,
            ':link'            => substr($link, 0, 255), // Link uzunluğunu sınırlamak için
            ':preview_html'    => $previewHtml
        ]);
        echo "Comment added successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üniversite Yorumları</title>
</head>
<body>
    <h1>Üniversite hakkında yorum yayınlayınız</h1>
    <form method="POST" action="">
        <label for="university_name">Üniversite Adı:</label><br>
        <input type="text" id="university_name" name="university_name" required><br><br>
        
        <label for="commenter_name">Adınız:</label><br>
        <input type="text" id="commenter_name" name="commenter_name" required><br><br>
        
        <label for="comment">Yorumunuz :</label><br>
        <textarea id="comment" name="comment" rows="4" required></textarea><br><br>
        
        <!-- Yeni Link alanı (opsiyonel) -->
        <label for="link">Bağlantı (ör. YouTube, Twitter):</label><br>
        <input type="text" id="link" name="link" placeholder="https://www.youtube.com/watch?v=..." ><br><br>
        
        <button type="submit">Yayınla</button>
    </form>

    <!-- Button to Redirect to index.php -->
    <form action="index.php" method="GET" style="margin-top: 20px;">
        <button type="submit">Başlangıç Sayfasına geri dön</button>
    </form>

    <h2>Yorumlar</h2>
    <?php
    try {
        // Fetch comments from the database
        $stmt = $pdo->query("
            SELECT university_name, commenter_name, comment, link, preview_html, created_at 
            FROM university_comments 
            ORDER BY created_at DESC
        ");
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comments as $c) {
            echo "<div style='border:1px solid #ccc; padding:10px; margin-bottom:10px;'>";
            echo "<h3>" . htmlspecialchars($c['university_name']) . "</h3>";
            echo "<p><strong>" . htmlspecialchars($c['commenter_name']) . ":</strong> " 
                 . nl2br(htmlspecialchars($c['comment'])) . "</p>";

            // Link önizlemesi
            if (!empty($c['link'])) {
                echo "<p><strong>Link Önizlemesi:</strong></p>";
                // preview_html'i direkt basıyoruz
                // (Gerçek projede XSS açısından güvenli olmaya dikkat etmelisin!)
                echo $c['preview_html'];
            }

            echo "<br><small>Posted on: " . $c['created_at'] . "</small>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
</body>
</html>