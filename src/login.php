<?php
// Start the session
session_start();

// Include database connection
include 'includes/config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Check if both fields are filled
    if (!empty($username) && !empty($password)) {
        // Fetch the user from the database
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables for the logged-in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
                    
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Giriş Yap</h1>

<?php if (!empty($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<!-- The form does not include any CSRF protection mechanism -->
<form action="login.php" method="POST">
    <label for="username">Kulanıcı Adı</label>
    <input type="text" name="username" id="username" placeholder="Adınızı giriniz" required><br>

    <label for="password">Şifre</label>
    <input type="password" name="password" id="password" placeholder="Şifrenizi Giriniz" required><br>

    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Burdan Kayıt Ol</a></p>

</body>
</html>
