<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

$userId = $_SESSION["user_id"] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && $userId) {
    $current = $_POST["current_password"] ?? '';
    $new = $_POST["new_password"] ?? '';
    $repeat = $_POST["new_password_repeat"] ?? '';

    if (!$current || !$new || !$repeat) {
        $_SESSION["message"] = "Tüm alanlar doldurulmalı.";
        header("Location: /hatter/RegisterSignIn/account.php");
        exit;
    }

    if ($new !== $repeat) {
        $_SESSION["message"] = "Yeni şifreler eşleşmiyor.";
        header("Location: /hatter/RegisterSignIn/account.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current, $user["password"])) {
        $_SESSION["message"] = "Mevcut şifre yanlış.";
        header("Location: /hatter/RegisterSignIn/account.php");
        exit;
    }

    $hashed = password_hash($new, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $success = $update->execute([$hashed, $userId]);

    if ($success) {
        $_SESSION["message"] = "Şifre başarıyla güncellendi.";
    } else {
        $_SESSION["message"] = "Bir hata oluştu.";
    }

    header("Location: /hatter/RegisterSignIn/account.php");
    exit;
}

$_SESSION["message"] = "İzin yok.";
header("Location: /hatter/RegisterSignIn/account.php");
exit;
