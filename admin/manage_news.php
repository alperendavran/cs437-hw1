<?php
include '../includes/config.php';

// harmfull file upload avaible 
// Handle adding new news
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $link = $_POST['link'];
    
    // Handle image upload
    $uploadDir = 'uploads/';
    $uploadedFile = $uploadDir . basename($_FILES['image']['name']);
    
    // **Vulnerable: No checks on file type or extension**
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadedFile);
    
    $sql = "INSERT INTO news (title, content, link, image, published_date) VALUES (:title, :content, :link, :image, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'link' => $link,
        'image' => $uploadedFile
    ]);
    
    echo "News added successfully. File uploaded: $uploadedFile";
}

$sql = "SELECT * FROM news ORDER BY published_date DESC";
$stmt = $pdo->query($sql);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage News</h1>
<!-- Redirect to dashboard.php -->
<a href="dashboard.php">
    <button type="button" style="margin-bottom: 20px;">Go to Dashboard</button>
</a>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required><br>
    <textarea name="content" placeholder="Content" required></textarea><br>
    <input type="url" name="link" placeholder="External Link"><br>
    <input type="file" name="image" required><br>
    <button type="submit">Add News</button>
</form>

<h2>All News</h2>
<?php foreach ($news as $item): ?>
    <div>
        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
        <p><?php echo htmlspecialchars($item['content']); ?></p>
        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Image" style="max-width: 200px;"><br>
        <a href="delete_news.php?id=<?php echo $item['id']; ?>">Delete</a>
    </div>
<?php endforeach; ?>
