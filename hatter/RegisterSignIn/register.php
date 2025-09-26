<?php
session_start(); // ✨ Oturumu başlat
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php"; // Veritabanı bağlantısı

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!$full_name || !$email || !$password) {
        $_SESSION["message"] = "Lütfen tüm alanları doldurun.";
        header("Location: http://localhost/hatter/RegisterSignIn/index.php#ty");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $_SESSION["message"] = "Bu e-posta zaten kayıtlı!";
        header("Location: giris.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password) VALUES (?, ?, ?, ?)");
    $username = explode("@", $email)[0];
    $stmt->execute([$username, $full_name, $email, $hashedPassword]);

    $_SESSION["message"] = "Kayıt başarılı! Giriş yapabilirsiniz.";
    header("Location: http://localhost/hatter/RegisterSignIn/index.php");
    exit;
}
?>
