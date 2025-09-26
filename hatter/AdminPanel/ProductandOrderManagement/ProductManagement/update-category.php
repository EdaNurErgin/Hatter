<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    header("Location: /hatter/index.php");
    exit;
}

// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryId = $_POST['categoryId'];  // Eski kategori ID'si
    $name = htmlspecialchars($_POST["categoryName"]);
    $slug = htmlspecialchars($_POST["categorySlug"]);

    // Eski resim
    $oldImage = $_POST["oldCategoryImage"]; 

    // Yeni resim yüklenmişse
    if (isset($_FILES["categoryImage"]) && $_FILES["categoryImage"]["error"] == 0) {
        $imagePath = "uploads/" . basename($_FILES["categoryImage"]["name"]);
        move_uploaded_file($_FILES["categoryImage"]["tmp_name"], "C:/xampp/htdocs/hatter/" . $imagePath);
    } else {
        // Yeni resim yoksa eski resmi kullan
        $imagePath = $oldImage;
    }

    // Veritabanını güncelle
    $sql = "UPDATE categories SET name = ?, slug = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $slug, $imagePath, $categoryId]);

    // Başarıyla güncellendiğinde, kategori listesine yönlendir
    header("Location: /hatter/AdminPanel/ProductandOrderManagement/ProductManagement/");
    exit();
}
?>
