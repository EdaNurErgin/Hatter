<?php
// /hatter/checkout/submit.php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo "Method Not Allowed";
  exit;
}

// CSRF
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  http_response_code(400);
  echo "Geçersiz istek (CSRF).";
  exit;
}

// Sepet
$cart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
  header('Location: /hatter/Products/index.php');
  exit;
}

// Form alanları (basit trim + zorunlu)
$full_name = trim($_POST['full_name'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$address   = trim($_POST['address'] ?? '');

if ($full_name === '' || $phone === '' || $address === '') {
  http_response_code(400);
  echo "Lütfen tüm alanları doldurun.";
  exit;
}

// Toplam hesapla
$total = 0.0;
foreach ($cart as $it) {
  $total += ((float)$it['price'] * (int)$it['qty']);
}

// DB
$DOCROOT = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
$APP = $DOCROOT . '/hatter';
require_once $APP . '/sqlBaglanti/db.php'; // $conn (PDO)

// Siparişi kaydet — transaction
try {
  $conn->beginTransaction();

  // Orders tablosu
  // user_id opsiyonel — giriş yapanlar için $_SESSION['user_id'] varsa kullan
  $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

  $stmt = $conn->prepare("
    INSERT INTO orders (user_id, full_name, phone, address, total_amount, created_at)
    VALUES (:uid, :name, :phone, :address, :total, NOW())
  ");
  $stmt->execute([
    ':uid'     => $user_id,
    ':name'    => $full_name,
    ':phone'   => $phone,
    ':address' => $address,
    ':total'   => $total,
  ]);

  $order_id = (int)$conn->lastInsertId();

  // OrderItems
  $oi = $conn->prepare("
    INSERT INTO order_items (order_id, product_id, price, qty, subtotal)
    VALUES (:oid, :pid, :price, :qty, :sub)
  ");

  // Stok düşmek istersen products.stock güncelle
  $decStock = $conn->prepare("
    UPDATE products SET stock = stock - :qty WHERE id = :pid AND stock >= :qty
  ");

  foreach ($cart as $it) {
    $pid = (int)$it['id'];
    $qty = (int)$it['qty'];
    $price = (float)$it['price'];
    $sub = $price * $qty;

    // stok kontrol (opsiyonel – istersen sıkılaştır)
    $decStock->execute([':qty' => $qty, ':pid' => $pid]);
    if ($decStock->rowCount() === 0) {
      // stok yetmediyse işlemi iptal et
      $conn->rollBack();
      http_response_code(409);
      echo "Stok yetersiz (Ürün ID: {$pid}).";
      exit;
    }

    $oi->execute([
      ':oid'   => $order_id,
      ':pid'   => $pid,
      ':price' => $price,
      ':qty'   => $qty,
      ':sub'   => $sub,
    ]);
  }

  $conn->commit();

  // Sepeti temizle
  unset($_SESSION['cart']);
  // CSRF yenile
  unset($_SESSION['csrf']);

  // Basit bir teşekkür sayfası
  header('Location: /hatter/checkout/thankyou.php?order_id='.$order_id);
  exit;

} catch (Throwable $e) {
  if ($conn->inTransaction()) { $conn->rollBack(); }
  http_response_code(500);
  echo "Sipariş oluşturulurken bir hata oluştu: " . htmlspecialchars($e->getMessage());
}
