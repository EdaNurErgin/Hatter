<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    header("Location: /hatter/index.php");
    exit;
}

// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// Kategori ID'si
$categoryId = $_POST['id'];

// Kategoriyi sil
$sql = "DELETE FROM categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$categoryId]);

// Silme işlemi başarılıysa, kategori listeleme sayfasına yönlendir
echo "Kategori başarıyla silindi.";
?>
