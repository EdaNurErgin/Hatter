<?php
// /hatter/checkout/index.php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

// Sepet yoksa ürünler sayfasına yönlendir
$cart = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) {
  header('Location: /hatter/Products/index.php');
  exit;
}

// CSRF token (basit)
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// Görsel yolu yardımcı
if (!function_exists('asset_url')) {
  function asset_url(string $path): string {
    $p = str_replace('\\','/', trim($path));
    if ($p !== '' && $p[0] !== '/') { $p = '/hatter/' . ltrim($p, '/'); }
    return htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
  }
}

// Toplam
$cartTotal = 0.0;
foreach ($cart as $it) {
  $cartTotal += ((float)$it['price'] * (int)$it['qty']);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout — Hatter</title>
  <link rel="stylesheet" href="/hatter/styles/style.css">
  <style>
    .checkout-page{max-width:1100px;margin:2rem auto;padding:2rem;background:#fff;border-radius:1.5rem}
    .co-grid{display:grid;grid-template-columns:2fr 1fr;gap:2rem}
    .co-card{border:1px solid #eee;border-radius:1rem;padding:1.2rem}
    .co-h{font-size:2.2rem;margin-bottom:1rem}
    .co-items{display:flex;flex-direction:column;gap:1rem;max-height:50vh;overflow:auto}
    .co-row{display:grid;grid-template-columns:70px 1fr auto;gap:1rem;align-items:center}
    .co-row img{width:70px;height:70px;object-fit:cover;border-radius:.6rem}
    .co-summary .line{display:flex;justify-content:space-between;margin:.4rem 0}
    .co-form input, .co-form textarea{width:100%;padding:.9rem;border:1px solid #ddd;border-radius:.6rem;margin-bottom:1rem;font-size:1.4rem}
    .co-form textarea{min-height:90px;resize:vertical}
    .co-submit{width:100%;padding:1.1rem;border:none;border-radius:1rem;background:var(--main-color);color:#fff;font-weight:700;font-size:1.6rem;cursor:pointer}
    .muted{color:#666;font-size:1.3rem}
  </style>
</head>
<body>

<?php include $_SERVER['DOCUMENT_ROOT'].'/hatter/root/header.php'; ?>

<section class="checkout-page">
  <h1 class="heading">order <span></span></h1>

  <div class="co-grid">
    <!-- Sol: Sepet Özeti -->
    <div class="co-card">
      <div class="co-h">Your Card</div>
      <div class="co-items" role="list">
        <?php foreach ($cart as $it): ?>
          <div class="co-row" role="listitem">
            <img src="<?= asset_url($it['image']) ?>" alt="<?= htmlspecialchars($it['name']) ?>">
            <div>
              <div style="font-weight:600;"><?= htmlspecialchars($it['name']) ?></div>
              <div class="muted">
                Adet: <?= (int)$it['qty'] ?> •
                Birim: $<?= number_format((float)$it['price'], 2) ?>
              </div>
            </div>
            <div>
              $<?= number_format((float)$it['price'] * (int)$it['qty'], 2) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Sağ: Teslimat + Toplam -->
    <div class="co-card co-summary">
      <div class="co-h">Delivery and Payment</div>

      <form class="co-form" method="post" action="/hatter/checkout/submit.php" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <label>Name and Surname</label>
        <input type="text" name="full_name" required placeholder="Adınız Soyadınız"/>

        <label>Phone</label>
        <input type="text" name="phone" required placeholder="+90..." />

        <label>Address</label>
        <textarea name="address" required placeholder="Açık adres"></textarea>

        <div class="line"><span>subtotal</span><strong>$<?= number_format($cartTotal,2) ?></strong></div>
        <div class="line"><span>Cargo</span><strong>$0.00</strong></div>
        <div class="line" style="border-top:1px solid #eee;padding-top:.6rem"><span>General Total</span><strong>$<?= number_format($cartTotal,2) ?></strong></div>

        <button class="co-submit" type="submit">Order</button>
      </form>
    </div>
  </div>
</section>

<?php include $_SERVER['DOCUMENT_ROOT'].'/hatter/root/footer.php'; ?>
<script src="/hatter/js/script.js"></script>
</body>
</html>
