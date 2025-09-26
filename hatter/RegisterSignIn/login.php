<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
//     var_dump($user);
// exit;
    

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["is_admin"] = $user["is_admin"];

        $_SESSION["message"] = "Giriş başarılı! Hoş geldin {$_SESSION["full_name"]}";

        if ($user["is_admin"]) {
            // Eğer adminse admin paneline yönlendir
            header("Location: http://localhost/hatter/AdminPanel/index.php");
        } else {
            // Normal kullanıcıysa ana sayfaya yönlendir
            header("Location: http://localhost/hatter/index.php");
        }
        exit;
    } else {
        $_SESSION["message"] = "Hatalı e-posta veya şifre!";
        header("Location: http://localhost/hatter/RegisterSignIn/index.php");
        exit;
    }
}




?>


