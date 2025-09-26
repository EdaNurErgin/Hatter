<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// ADRES EKLEME
// Sadece POST isteğiyle çalış
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION["user_id"] ?? null;
    $content = trim($_POST["content"] ?? '');

    if ($userId && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO address (content, user_id) VALUES (?, ?)");
        $stmt->execute([$content, $userId]);

        // AJAX çağrısı varsa "başarılı" cevabı gönder
        echo "eklendi";
    } else {
        echo "eksik veri";
    }
} else {
    echo "geçersiz istek";
}





?>
