<?php
// /hatter/cart/add.php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$DOCROOT = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
$APP     = $DOCROOT . '/hatter';

require_once $APP . '/sqlBaglanti/db.php'; // $conn (PDO)

function is_json_request(): bool {
  $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
  if (stripos($accept, 'application/json') !== false) return true;
  $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
  return strtolower($xhr) === 'xmlhttprequest';
}
function cart_totals(array $cart): array {
  $total = 0.0; $count = 0;
  foreach ($cart as $it) { $total += (float)$it['price'] * (int)$it['qty']; $count += (int)$it['qty']; }
  return [$total, $count];
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  if (is_json_request()) {
    header('Content-Type: application/json; charset=utf-8', true, 400);
    echo json_encode(['ok'=>false,'msg'=>'Geçersiz ürün id'], JSON_UNESCAPED_UNICODE);
    exit;
  }
  http_response_code(400); die('Geçersiz ürün');
}

$stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
  if (is_json_request()) {
    header('Content-Type: application/json; charset=utf-8', true, 404);
    echo json_encode(['ok'=>false,'msg'=>'Ürün bulunamadı'], JSON_UNESCAPED_UNICODE);
    exit;
  }
  http_response_code(404); die('Ürün yok');
}

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['cart'][$id])) {
  $_SESSION['cart'][$id] = [
    'id'    => (int)$product['id'],
    'name'  => (string)$product['name'],
    'price' => (float)$product['price'],
    'image' => (string)$product['image'],
    'qty'   => 1,
  ];
} else {
  $_SESSION['cart'][$id]['qty'] += 1;
}

if (is_json_request()) {
  [$total, $count] = cart_totals($_SESSION['cart']);

  ob_start(); ?>
  <?php foreach ($_SESSION['cart'] as $it): ?>
    <div class="cart-item" role="listitem">
      <a class="remove"
         href="/hatter/cart/remove.php?id=<?= (int)$it['id'] ?>&r=<?= urlencode($_SERVER['HTTP_REFERER'] ?? '/hatter/index.php') ?>"
         title="Kaldır" aria-label="Ürünü sepetten kaldır">
        <i class="fas fa-times" aria-hidden="true"></i>
      </a>
      <img src="/hatter/<?= htmlspecialchars(ltrim($it['image'], '/')) ?>" alt="<?= htmlspecialchars($it['name']) ?>">
      <div class="content">
        <h3 style="margin:0;"><?= htmlspecialchars($it['name']) ?></h3>
        <div class="price">
          $<?= number_format((float)$it['price'], 2) ?> × <?= (int)$it['qty'] ?>
          = <strong>$<?= number_format((float)$it['price'] * (int)$it['qty'], 2) ?></strong>
        </div>
      </div>
    </div>
  <?php endforeach;
  $items_html = ob_get_clean();

  ob_start(); ?>
  <div class="summary-row">
    <span>Toplam</span>
    <strong>$<?= number_format($total, 2) ?></strong>
  </div>
  <a href="/hatter/checkout/index.php" class="btn w-full" aria-label="Satın alma adımına geç">check out now</a>
  <?php
  $summary_html = ob_get_clean();

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode([
    'ok'           => true,
    'count'        => (int)$count,
    'total'        => number_format($total, 2),
    'items_html'   => $items_html,
    'summary_html' => $summary_html,
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

// JSON değilse normal geri dönüş
$back = !empty($_GET['r']) ? $_GET['r'] : ($_SERVER['HTTP_REFERER'] ?? '/hatter/Products/index.php');
header('Location: ' . $back);
exit;
