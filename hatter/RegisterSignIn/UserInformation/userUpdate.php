<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

$userId = $_SESSION["user_id"] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && $userId) {
    $full_name = trim($_POST["full_name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $phone = trim($_POST["phone"] ?? '');

    $fields = [];
    $values = [];

    if ($full_name !== '') {
        $fields[] = "full_name = ?";
        $values[] = $full_name;
    }

    if ($email !== '') {
        $fields[] = "email = ?";
        $values[] = $email;
    }

    if ($phone !== '') {
        $fields[] = "phone = ?";
        $values[] = $phone;
    }

    if (!empty($fields)) {
        $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
        $values[] = $userId;
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute($values);

        $_SESSION["message"] = $success
            ? "Bilgiler başarıyla güncellendi."
            : "Güncelleme sırasında bir hata oluştu.";
    } else {
        $_SESSION["message"] = "Güncellenecek bir bilgi girilmedi.";
    }

    header("Location: /hatter/RegisterSignIn/account.php");
    exit;
}

$_SESSION["message"] = "İzin yok.";
header("Location: /hatter/RegisterSignIn/account.php");
exit;
