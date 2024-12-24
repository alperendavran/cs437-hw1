<?php
session_start();
include 'includes/config.php';

// Capture the search query from the form submission
$search_query = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Advice</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Welcome to the Turkish University Advice Website</h1>
    
    <!-- Check if user is logged in -->
    <nav>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
            
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin/dashboard.php">Admin Dashboard</a>
            <?php endif; ?>
            
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> 
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <h2>Recent University News</h2>

    <!-- Search Form -->
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search for university news..." value="<?php echo htmlspecialchars($search_query); ?>" />
        <button type="submit">Search</button>
    </form>

    <?php
    // URL of the RSS feed
    $rss_url = 'https://www.haberturk.com/rss';

    // Function to load RSS feed using cURL
    function load_rss_via_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    // Keywords to filter university-related news
    $university_keywords = ['üniversite', 'fakülte', 'rektör', 'öğrenci', 'yükseköğretim', 'kampüs', 'burs', 'akademik', 'eğitim', 'lisans', 'yüksek lisans', 'doktora'];

    // Load and parse RSS feed
    $rss_data = load_rss_via_curl($rss_url);
    $rss = simplexml_load_string($rss_data);

    if ($rss === false) {
        echo "<p>Failed to load RSS feed from <a href='$rss_url'>$rss_url</a>. Please try again later.</p>";
    } else {
        $university_news_found = false;
        $news_count = 0; // Counter for limiting the number of news items displayed
        
        if (!empty($rss->channel->item)) {
            foreach ($rss->channel->item as $item) {
                $title = strtolower($item->title);
                $description = strtolower($item->description);
                
                // Check if the title or description contains any of the university-related keywords
                $is_university_news = false;
                foreach ($university_keywords as $keyword) {
                    if (strpos($title, $keyword) !== false || strpos($description, $keyword) !== false) {
                        $is_university_news = true;
                        break; // No need to check other keywords
                    }
                }

                // If the news is related to universities, also check for the search term if provided
                if ($is_university_news) {
                    if ($search_query === '' || strpos($title, $search_query) !== false || strpos($description, $search_query) !== false) {
                        echo "<div>";
                        echo "<h3><a href='{$item->link}' target='_blank'>{$item->title}</a></h3>";
                        echo "<p>{$item->description}</p>";
                        echo "<hr>";
                        echo "</div>";
                        $university_news_found = true;
                        $news_count++; // Increment the counter

                        // Stop the loop once 45 news items have been displayed
                        if ($news_count >= 45) {
                            break; // Break from the loop when 45 news items are displayed
                        }
                    }
                }
            }

            if (!$university_news_found) {
                echo "<p>No university-related news items available at this moment.</p>";
            }
        } else {
            echo "<p>No news items available at this moment.</p>";
        }
    }
    ?>

    <!-- Add Button to Comments Page -->
    <div>
        <a href="comments.php" class="button">Go to Comments</a>
    </div>
</main>



</body>
</html>
