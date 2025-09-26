<?php
declare(strict_types=1);
session_start();

header('Content-Type: application/json; charset=utf-8');

/* Admin kontrolü */
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'Yetkisiz']); exit;
}

/* CSRF */
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Geçersiz istek']); exit;
}

/* Parametreler */
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = $_POST['status'] ?? '';
$allowed = ['active','preparing','shipped','cancelled'];
if ($id<=0 || !in_array($status, $allowed, true)) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Parametre hatası']); exit;
}

/* DB */
$pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* Var mı? */
$chk = $pdo->prepare("SELECT id,status FROM orders WHERE id=:id");
$chk->execute([':id'=>$id]);
$curr = $chk->fetch(PDO::FETCH_ASSOC);
if (!$curr) { echo json_encode(['ok'=>false,'error'=>'Sipariş yok']); exit; }

/* Durum güncelle */
$u = $pdo->prepare("UPDATE orders SET status=:s WHERE id=:id");
$u->execute([':s'=>$status, ':id'=>$id]);

echo json_encode(['ok'=>true]);
