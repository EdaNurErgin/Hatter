<?php
// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// Kategori ID'si alındı mı?
if (isset($_GET['id'])) {
    $categoryId = $_GET['id'];

    // Kategoriyi ID'ye göre veritabanından al
    $sql = "SELECT * FROM categories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$categoryId]);

    if ($stmt->rowCount() > 0) {
        // Kategoriyi JSON formatında döndürelim
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        // Kategori bulunamazsa hata mesajı döndür
        echo json_encode(["error" => "Kategori bulunamadı."]);
    }
} else {
    // ID parametresi gelmediyse hata mesajı döndür
    echo json_encode(["error" => "Kategori ID'si belirtilmemiş."]);
}
?>
