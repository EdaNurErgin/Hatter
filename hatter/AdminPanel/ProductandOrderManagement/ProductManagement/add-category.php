<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    header("Location: /hatter/index.php");
    exit;
}

// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["categoryName"]);
    $slug = !empty($_POST["categorySlug"]) ? htmlspecialchars($_POST["categorySlug"]) : strtolower(str_replace(' ', '-', $name));

    // Resim yükleme
    $imagePath = null;
    if (isset($_FILES["categoryImage"]) && $_FILES["categoryImage"]["error"] == 0) {
        $imagePath = "uploads/" . basename($_FILES["categoryImage"]["name"]);
        move_uploaded_file($_FILES["categoryImage"]["tmp_name"], "C:/xampp/htdocs/hatter/" . $imagePath);
    }

    // SQL sorgusu ile kategoriyi ekle
    $sql = "INSERT INTO categories (name, slug, image) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $slug, $imagePath]);

    // Yönlendirme
    header("Location: /hatter/AdminPanel/ProductandOrderManagement/ProductManagement/");
    exit();
}
?>
