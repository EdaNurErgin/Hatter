<?php
declare(strict_types=1);

// Sadece JSON çıktı üret
error_reporting(E_ALL);
ini_set('display_errors', '0');

set_exception_handler(function (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'exception', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
});

session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'forbidden'], JSON_UNESCAPED_UNICODE);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // TABLONA GÖRE ALANLAR
    $sql = 'SELECT id, title, subtitle, content1, content2, content3, image_url, updated_at
            FROM about_page
            ORDER BY id
            LIMIT 1';
    $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);

    echo json_encode($row ?: new stdClass(), JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    // Buraya düşerse zaten exception_handler devreye girecek, ama yine de:
    http_response_code(500);
    echo json_encode(['error' => 'db', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
