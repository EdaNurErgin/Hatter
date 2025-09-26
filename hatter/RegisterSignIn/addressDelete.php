<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

$userId = $_SESSION["user_id"] ?? null;
$adresId = $_POST["id"] ?? null; // 🔁 id olarak değiştiriyoruz

if (!$userId || !$adresId) {
    echo "eksik veri";
    exit;
}

// Kullanıcıya ait mi kontrol et
$stmt = $conn->prepare("DELETE FROM address WHERE id = ? AND user_id = ?");
$result = $stmt->execute([$adresId, $userId]);

echo $result ? "silindi" : "silinemedi";
