<?php
include 'includes/config.php';

// Fetch all news from the database
$sql = "SELECT * FROM news ORDER BY published_date DESC";
$stmt = $pdo->query($sql);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .news-item {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .news-item img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 10px;
        }
        .news-title {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .news-content {
            margin-bottom: 10px;
        }
        .news-link {
            color: #007BFF;
            text-decoration: none;
        }
        .news-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Haberler</h1>

    <?php foreach ($news as $item): ?>
        <div class="news-item">
            <h2 class="news-title"><?php echo htmlspecialchars($item['title']); ?></h2>
            <p class="news-content"><?php echo htmlspecialchars($item['content']); ?></p>
            <?php if (!empty($item['image'])): ?>
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
            <?php endif; ?>
            <?php if (!empty($item['link'])): ?>
                <p><a href="<?php echo htmlspecialchars($item['link']); ?>" class="news-link" target="_blank">Daha fazlası için</a></p>
            <?php endif; ?>
            <p><small>Şu tarihte Yayınlandı: <?php echo htmlspecialchars($item['published_date']); ?></small></p>
        </div>
    <?php endforeach; ?>

    <?php if (empty($news)): ?>
        <p>Admin haberi mevcut değil</p>
    <?php endif; ?>
</body>
</html>
