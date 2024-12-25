<?php


include '../includes/config.php';


$blacklist = [
    'example.com',
    'malicious-site.net',
    'banned-keyword',
    '127.0.0.1',
    'localhost',
    '0.0.0.0',

];

// Function to check if a link is blacklisted
function isBlacklisted($link, $blacklist) {
    foreach ($blacklist as $blocked) {
        if (stripos($link, $blocked) !== false) {
            return true;
        }
    }
    return false;
}

// Handle adding new news
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $link = $_POST['link'];

    // Check if the link is blacklisted
    if (isBlacklisted($link, $blacklist)) {
        die("Error: The provided link is not allowed.");
    }
    
    // Handle image upload
    $uploadDir = 'uploads/';
    $uploadedFile = $uploadDir . basename($_FILES['image']['name']);
    
    // Upload to current directory
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadedFile);

    // Copy to upper directory
    $upperUploadDir = '../uploads/';
    if (!is_dir($upperUploadDir)) {
        mkdir($upperUploadDir, 0755, true);
    }
    $upperUploadedFile = $upperUploadDir . basename($_FILES['image']['name']);
    copy($uploadedFile, $upperUploadedFile);
    
    $sql = "INSERT INTO news (title, content, link, image, published_date) VALUES (:title, :content, :link, :image, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'link' => $link,
        'image' => $uploadedFile
    ]);
    
    echo "News added successfully. File uploaded to both directories: $uploadedFile and $upperUploadedFile";
}

$sql = "SELECT * FROM news ORDER BY published_date DESC";
$stmt = $pdo->query($sql);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage News</h1>
<!-- Redirect to dashboard.php -->
<a href="dashboard.php">
    <button type="button" style="margin-bottom: 20px;">Admin panosuna dön</button>
</a>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Başlık" required><br>
    <textarea name="content" placeholder="Konu" required></textarea><br>
    <input type="url" name="link" placeholder="Bağlantı Ekle"><br>
    <input type="file" name="image" required><br>
    <button type="submit">Haber Ekle</button>
</form>

<h2>All News</h2>
<?php foreach ($news as $item): ?>
    <div>
        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
        <p><?php echo htmlspecialchars($item['content']); ?></p>
        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Image" style="max-width: 200px;"><br>
        <a href="delete_news.php?id=<?php echo $item['id']; ?>">Sil</a>
    </div>
<?php endforeach; ?>
