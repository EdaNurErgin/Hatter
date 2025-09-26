<?php
// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen verileri al
    $name = htmlspecialchars($_POST["productName"]);
    $price = $_POST["productPrice"];
    $category = $_POST["productCategory"];
    $stock = $_POST["productStock"];
    $description = htmlspecialchars($_POST["productDescription"]);

    // Resmi yükleme
    if (isset($_FILES["productImage"]) && $_FILES["productImage"]["error"] == 0) {
        $imagePath = "uploads/" . basename($_FILES["productImage"]["name"]);
        move_uploaded_file($_FILES["productImage"]["tmp_name"], "C:/xampp/htdocs/hatter/" . $imagePath);
    }

    // SQL sorgusu ile verileri veritabanına ekle
    $sql = "INSERT INTO products (name, price, category_id, stock, description, image) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $category, $stock, $description, $imagePath]);

    // Yönlendirme
    header("Location: /hatter/AdminPanel/ProductandOrderManagement/ProductManagement/");
    exit();
}
?>
