<?php
declare(strict_types=1); // 1. SATIRDA!
session_start();

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    exit('Yetkisiz erişim');
}

require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('Geçersiz mesaj ID');
}

$sql = "DELETE FROM contact_messages WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

echo $stmt->execute() ? "Mesaj başarıyla silindi." : "Mesaj silinirken bir hata oluştu.";
