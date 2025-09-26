<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Teşekkürler</title>
  <link rel="stylesheet" href="/hatter/styles/style.css">
  <style>
    .ty-card{
      max-width:800px;
      margin:2.5rem auto;
      background:#fff;
      border-radius:1.5rem;
      padding:2.2rem;
      text-align:center;
      box-shadow:0 6px 18px rgba(0,0,0,.06);
    }
    .ty-title{ font-size:2.6rem; font-weight:800; letter-spacing:-.02em; margin-bottom:.6rem; }
    .ty-sub{ color:#6b7280; font-size:1.4rem; margin-bottom:1.2rem; }
    .footer { position: fixed; left:0; right:0; bottom:0; z-index: 100; }
    body { padding-bottom: 140px; } /* footer yüksekliği kadar */

  </style>
</head>
<body>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/hatter/root/header.php'; ?>

  <!-- İÇERİK mutlaka main.site-main içinde olsun -->
  <main class="site-main">
    <section class="page-shell">
      <div class="ty-card">
        <h1 class="ty-title">Received</h1>
        <p class="ty-sub">Your order has been received successfully.</p>

        <?php if ($order_id): ?>
          <p>Order ID: <strong>#<?= $order_id ?></strong></p>
        <?php endif; ?>

        <a class="btn" href="/hatter/Products/index.php">Continue shopping</a>
      </div>
    </section>
  </main>

  <?php include $_SERVER['DOCUMENT_ROOT'].'/hatter/root/footer.php'; ?>
</body>
</html>
