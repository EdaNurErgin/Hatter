<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    echo "Yetkisiz giriş!";
    exit;
}

// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productId = intval($_POST["id"]);

    // Ürünü veritabanından sil
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute([$productId]);

    if ($success) {
        echo "Ürün başarıyla silindi.";
    } else {
        http_response_code(500);
        echo "Ürün silinirken hata oluştu.";
    }
}
?>
