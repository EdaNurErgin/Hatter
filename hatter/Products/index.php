<?php
// Hataları gör
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* --- Yol sabitleri --- */
// C:/xampp/htdocs
$DOCROOT = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
// C:/xampp/htdocs/hatter
$APP = $DOCROOT . '/hatter';

/* --- DB bağlantısı --- */
require_once $APP . '/sqlBaglanti/db.php'; // $conn (PDO) burada oluşuyor

/* --- Ürünleri çek --- */
try {
    $stmt = $conn->query("SELECT id, name, description, price, stock, category_id, image FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('Sorgu hatası: ' . $e->getMessage());
}

/* --- Görsel yolu düzeltici (uploads/kk.jpg -> /hatter/uploads/kk.jpg) --- */
function asset_url(string $path): string {
    $p = str_replace('\\','/', trim($path));
    if ($p !== '' && $p[0] !== '/') { $p = '/hatter/' . ltrim($p, '/'); }
    return htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
}
// kATEGORİ Filtresi
$where = '';
$params = [];
if (!empty($_GET['category'])) {
    $where = "WHERE c.name = :cat";
    $params[':cat'] = $_GET['category'];
}

$sql = "
  SELECT p.id, p.name, p.description, p.price, p.stock, p.image, c.name AS category_name
  FROM products p
  LEFT JOIN categories c ON c.id = p.category_id
  $where
  ORDER BY p.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hatter</title>
    <link rel="stylesheet" href="/hatter/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php
$header = $APP . '/root/header.php';
if (file_exists($header)) include $header;
?>
<section class="products">
  <h1 class="heading">
    <?php if (!empty($_GET['category'])): ?>
        <?= htmlspecialchars($_GET['category']) ?> <span>Hats</span>
    <?php else: ?>
        All <span>Products</span>
    <?php endif; ?>
  </h1>

  <div class="box-container">
    <?php if (empty($products)): ?>
        <p style="padding:1rem;">Bu kategoriye ait ürün bulunamadı.</p>
    <?php else: foreach ($products as $p): ?>
        <div class="box" data-href="/hatter/productMain/index.php?id=<?= (int)$p['id'] ?>">
            <div class="box-head">
                <span class="title"><?= htmlspecialchars($p['category_name']) ?></span>
                <a href="/hatter/productMain/index.php?id=<?= (int)$p['id'] ?>" class="name">
                    <?= htmlspecialchars($p['name']) ?>
                </a>
            </div>
            <div class="image">
                <img src="<?= asset_url($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            </div>
            <div class="box-bottom">
                <div class="info">
                    <b class="price">$<?= number_format((float)$p['price'], 2) ?></b>
                </div>
                <div class="products-btn">
                    <a href="/hatter/cart/add.php?id=<?= (int)$p['id'] ?>" class="add-to-cart"
                    onclick="event.stopPropagation();">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>
  </div>
</section>


<script>
// Kutunun tamamını tıklanır yap
document.addEventListener('click', function(e){
  const box = e.target.closest('.box[data-href]');
  if (box) {
    window.location.href = box.getAttribute('data-href');
  }
});

// Klavye ile erişilebilirlik (Enter ile git)
document.addEventListener('keydown', function(e){
  if (e.key === 'Enter') {
    const box = document.activeElement.closest('.box[data-href]');
    if (box) {
      window.location.href = box.getAttribute('data-href');
    }
  }
});
</script>

<?php
$footer = $APP . '/root/footer.php';
if (file_exists($footer)) include $footer;
?>

</body>
</html>
