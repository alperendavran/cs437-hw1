<?php
// os command injection (blind)
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // BLIND VULNERABILITY: User input in a shell command without output
    // we are not cehhcing username for os 
    shell_exec("ping -c 1 " . $username);

    echo "WELCOME " . htmlspecialchars($username) . "<br>";

    $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);

    echo "Kullanıcı Başarıyla oluşturuldu! <a href='login.php'>Login here</a>";
}
?>
<form method="POST">
    <input type="text" name="username" placeholder="Kullanıcı Adı" required><br>
    <input type="email" name="email" placeholder="E-Posta" required><br>
    <input type="password" name="password" placeholder="Şifre" required><br>
    <button type="submit">Kayıt OL</button>
</form>
