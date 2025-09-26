<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    header("Location: /hatter/index.php");
    exit;
}

require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST["productId"]);
    $name = htmlspecialchars($_POST["productName"]);
    $price = $_POST["productPrice"];
    $category = $_POST["productCategory"];
    $stock = $_POST["productStock"];
    $description = htmlspecialchars($_POST["productDescription"]);
    $oldImage = $_POST["oldProductImage"];

    // Yeni resim yüklendi mi kontrol
    if (isset($_FILES["productImage"]) && $_FILES["productImage"]["error"] == 0) {
        // Yeni resim yüklendiyse onu kaydet
        $imagePath = "uploads/" . basename($_FILES["productImage"]["name"]);
        move_uploaded_file($_FILES["productImage"]["tmp_name"], "C:/xampp/htdocs/hatter/" . $imagePath);
    } else {
        // Yüklenmediyse eski resim aynen kullanılacak
        $imagePath = $oldImage;
    }

    // Güncelleme sorgusu
    $sql = "UPDATE products SET name = ?, price = ?, category_id = ?, stock = ?, description = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$name, $price, $category, $stock, $description, $imagePath, $id]);

    // Güncellemeden sonra listeye geri dön
    header("Location: /hatter/AdminPanel/ProductandOrderManagement/ProductManagement/");
    exit();
}
?>
