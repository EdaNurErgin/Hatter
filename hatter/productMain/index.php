<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* --- Yol sabitleri --- */
$DOCROOT = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
$APP     = $DOCROOT . '/hatter';

/* --- DB bağlantısı --- */
require_once $APP . '/sqlBaglanti/db.php'; // $conn (PDO)

/* --- Helpers --- */
function asset_url(string $path): string {
    $p = str_replace('\\','/', trim($path));
    if ($p !== '' && $p[0] !== '/') { $p = '/hatter/' . ltrim($p, '/'); }
    return htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
}

/* --- Parametre & veri --- */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); die('Geçersiz ürün.'); }

/* Kategori adını da çekelim */
$stmt = $conn->prepare("
  SELECT p.id, p.name, p.description, p.price, p.stock, p.category_id, p.image,
         c.name AS category_name
  FROM products p
  LEFT JOIN categories c ON c.id = p.category_id
  WHERE p.id = :id
  LIMIT 1
");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) { http_response_code(404); die('Ürün bulunamadı.'); }

/* Görseller */
$images = [];
if (!empty($product['image'])) {
    foreach (preg_split('/[;,]/', $product['image']) as $img) {
        $img = trim($img);
        if ($img !== '') { $images[] = asset_url($img); }
    }
}
$mainImage = $images[0] ?? '/hatter/images/products/kk.jpg';

/* Fiyat / stok */
$priceText = number_format((float)$product['price'], 0, ',', '.');
$stockInt  = (int)$product['stock'];
$stockText = $stockInt > 0 ? "Stokta • {$stockInt} adet" : "Stokta yok";
$stockClr  = $stockInt > 0 ? "#198754" : "#c1121f";

/* Basit SKU (gerekirse) */
$sku = 'HTR-' . str_pad((string)$product['id'], 5, '0', STR_PAD_LEFT);

/* Tip bilgisi (kategori adı yoksa default) */
$productType = $product['category_name'] ?: 'Hats';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hatter</title>
  <link rel="stylesheet" href="/hatter/styles/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
  <style>
    /* Product Information kutusunu sayfayla aynı hizada ve doğru genişlikte göster */
    .product-description{
      margin:4rem auto 0;
      padding:3rem;               /* 7% yerine sabit padding */
      background:#f7f7f7;
      border-radius:2rem;
      max-width:1100px;           /* sayfa genişliğine uyumlu */
      width:100%;
      box-sizing:border-box;
    }
    /* İç grid ve kutular mevcut CSS'in varsa ona bırak; yoksa minimal stiller: */
    .desc-grid{display:grid;gap:1rem;grid-template-columns:1.5fr 1fr;}
    @media(max-width:900px){.desc-grid{grid-template-columns:1fr;}}
    .desc-box{border:1px solid #f0f0f0;border-radius:1rem;padding:1rem 1.2rem;background:#fff;}
    .spec-table{width:100%;border-collapse:collapse}
    .spec-table th,.spec-table td{padding:.6rem .5rem;border-bottom:1px solid #f3f3f3;text-align:left}
    .dot{display:inline-block;width:.55rem;height:.55rem;border-radius:50%;margin-right:.35rem;vertical-align:middle;background:currentColor}
  </style>
</head>
<body>

<?php include $APP . '/root/header.php'; ?>

<section class="product-page">
  <div class="product-detail">
    <div class="product-cover">
      <!-- Left: Product Image -->
      <div class="image-gallery">
        <img src="<?= htmlspecialchars($mainImage) ?>" alt="product image" class="main-image" id="mainProductImage">
      </div>

      <!-- Right: Product Info -->
      <div class="product-info">
        <!-- >>> İstenen TIP BİLGİSİ buraya geldi <<< -->
        <div class="product-type title">
          <?= htmlspecialchars($productType) ?>
        </div>

        <h2 class="product-title"><?= htmlspecialchars($product['name']) ?></h2>

        <div class="product-price">
          <?= $priceText ?> $
          <!-- <span class="badge-soft" style="margin-left:.5rem;color:<?= $stockClr ?>"><?= $stockText ?></span> -->
        </div>

        <div class="size-options">
          <button class="btn size-btn">S</button>
          <button class="btn size-btn">M</button>
          <button class="btn size-btn">L</button>
        </div>

        <div class="product-actions">
          <a class="btn" href="/hatter/checkout/buy-now.php?id=<?= (int)$product['id'] ?>">Hemen Al</a>
          <a class="btn btn-green" href="/hatter/cart/add.php?id=<?= (int)$product['id'] ?>">Sepete Ekle</a>
          <button class="favorite-btn" type="button" title="Favorilere ekle">
            <i class="far fa-heart"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Product Information -->
    <section class="product-description">
      <h3 class="desc-heading">Product Information</h3>

      <div class="desc-grid">
        <!-- Sol: Açıklama -->
        <div class="desc-box">
          <?php if (!empty($product['description'])): ?>
            <p style="margin:.25rem 0 0; line-height:1.6">
              <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>
          <?php else: ?>
            <ul>
              <li>Günlük kullanım için hafif ve konforlu tasarım</li>
              <li>Nefes alabilir kumaş, dört mevsime uygun</li>
              <li>Ayarlanabilir arka bant ile esnek kalıp</li>
              <li>Şık dikiş detayları ve modern kesim</li>
            </ul>
          <?php endif; ?>

          <div class="muted" style="margin-top:.9rem;font-size:.9rem;">
            * Görseldeki renkler ekran ayarlarına göre değişiklik gösterebilir.
          </div>
        </div>

        <!-- Sağ: Tek satır özellik/politika -->
        <aside class="desc-box">
          <table class="spec-table">
            <tr>
              <th>Stok Durumu</th>
              <td style="color:<?= $stockClr ?>">
                <span class="dot"></span><?= htmlspecialchars($stockText) ?>
              </td>
            </tr>
          </table>
        </aside>
      </div>
    </section>
    <!-- /Product Information -->
  </div>
</section>

<script>
document.querySelectorAll(".favorite-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    btn.classList.toggle("active");
    const icon = btn.querySelector("i");
    icon.classList.toggle("far");
    icon.classList.toggle("fas");
  });
});
</script>

<?php include $APP . '/root/footer.php'; ?>

</body>
</html>
