<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// Kullanıcı giriş yapmamışsa engelle
if (!isset($_SESSION["user_id"])) {
    die("Giriş yapmanız gerekiyor.");
}

$title = $_POST["title"];
$content = $_POST["content"];
$user_id = $_SESSION["user_id"];
$created_at = date("Y-m-d H:i:s");

$imagePathInDb = ""; // Varsayılan olarak boş

if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
    $uploadDir = "C:/xampp/htdocs/hatter/uploads/";
    $imageName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetPath = $uploadDir . $imageName;

    // Göreli yol (veritabanına kaydedilecek)
    $imagePathInDb = "/hatter/uploads/" . $imageName;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
        die("Resim yüklenemedi.");
    }
}

// Veritabanına kaydet
$stmt = $conn->prepare("INSERT INTO blog_posts (user_id, title, content, image_path, created_at) VALUES (?, ?, ?, ?, ?)");
$success = $stmt->execute([$user_id, $title, $content, $imagePathInDb, $created_at]);

if ($success) {
    header("Location: index.php");
    exit;
} else {
    echo "Kayıt sırasında hata oluştu.";
}
