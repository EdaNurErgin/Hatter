<?php
// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// Silinecek kullanıcının ID'sini al
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Kullanıcıyı silme sorgusu
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Kullanıcı başarıyla silindi.";
    } else {
        echo "Kullanıcı silinirken bir hata oluştu.";
    }
} else {
    echo "Geçersiz kullanıcı ID'si.";
}
?>
