<?php
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Postu sil
    $sql = "DELETE FROM blog_posts WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $post_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Post başarıyla silindi.";
    } else {
        echo "Post silinirken hata oluştu.";
    }
} else {
    echo "Geçersiz post ID'si.";
}
?>
