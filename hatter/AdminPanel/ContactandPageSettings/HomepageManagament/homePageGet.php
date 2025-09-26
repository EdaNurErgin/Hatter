<?php
session_start();

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'forbidden']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // İsteğe bağlı filtre: sadece aktifleri getir (URL’de ?active=1 gönderirsen)
    $onlyActive = isset($_GET['active']) && $_GET['active'] === '1';

    if ($onlyActive) {
        $stmt = $pdo->query("SELECT id, category_name, image_url, sort_order, is_active, created_at, updated_at
                             FROM `homepage`
                             WHERE is_active = 1
                             ORDER BY sort_order ASC, id ASC");
    } else {
        $stmt = $pdo->query("SELECT id, category_name, image_url, sort_order, is_active, created_at, updated_at
                             FROM `homepage`
                             ORDER BY sort_order ASC, id ASC");
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'db_error', 'message' => $e->getMessage()]);
}
