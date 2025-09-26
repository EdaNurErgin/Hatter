<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    echo json_encode(['success'=>false, 'message'=>'forbidden']);
    exit;
}

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) {
        throw new RuntimeException("Geçersiz ID");
    }

    // Kayıt var mı kontrol et
    $stmt = $pdo->prepare("SELECT image_url FROM homepage WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        throw new RuntimeException("Kayıt bulunamadı");
    }

    // Resim dosyasını da silelim
    if ($row['image_url']) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $row['image_url'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // DB’den sil
    $stmt = $pdo->prepare("DELETE FROM homepage WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success'=>true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
