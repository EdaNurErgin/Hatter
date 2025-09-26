<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
  header("Location: /hatter/index.php"); exit;
}

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function trStatus(string $s): string {
  return ['active'=>'Aktif','preparing'=>'Hazırlanıyor','shipped'=>'Kargoda','cancelled'=>'İptal'][$s] ?? $s;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); echo "Geçersiz talep."; exit; }

$pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* Sipariş başlık */
$h = $pdo->prepare("SELECT id, user_id, full_name, phone, address, total_amount, status, created_at
                    FROM orders WHERE id=:id");
$h->execute([':id'=>$id]);
$order = $h->fetch(PDO::FETCH_ASSOC);
if (!$order) { http_response_code(404); echo "Sipariş bulunamadı."; exit; }

/* Kalemler (+ ürün ismi varsa) */
$it = $pdo->prepare("
  SELECT oi.id, oi.product_id, oi.price, oi.qty, oi.subtotal,
         COALESCE(p.name, CONCAT('Ürün #', oi.product_id)) AS product_name,
         p.image AS product_image
  FROM order_items oi
  LEFT JOIN products p ON p.id = oi.product_id
  WHERE oi.order_id = :id
  ORDER BY oi.id ASC
");
$it->execute([':id'=>$id]);
$items = $it->fetchAll(PDO::FETCH_ASSOC);

$it->execute([':id'=>$id]);
$items = $it->fetchAll(PDO::FETCH_ASSOC);

/* Hesaplamalar */
$items_total = 0.0;
foreach ($items as $row) { $items_total += (float)$row['subtotal']; }
$total = $order['total_amount'] ? (float)$order['total_amount'] : $items_total;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Hatter — Sipariş #<?= (int)$order['id'] ?></title>
  <link rel="stylesheet" href="/hatter/styles/style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    .badge{display:inline-block;padding:.3rem .8rem;border-radius:.6rem;background:#eef;border:1px solid #dde;font-size:1.2rem}
    .meta{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem}
    .meta .card{background:#f9f9f9;border:1px solid #eee;border-radius:1rem;padding:1rem}
    .actions{display:flex;gap:.5rem;margin:1rem 0}
    .muted{opacity:.8}
    .thumb{width:56px;height:56px;object-fit:cover;border-radius:.6rem}
  </style>
</head>
<body>
<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>

<section class="admin-panel-layout" style="margin-top:16rem;">
  <aside class="admin-sidebar">
    <h2 class="admin-menu-btn-h3">Sipariş Detayı</h2>
    <a class="admin-menu-btn" href="/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/orders.php">← Sipariş listesine dön</a>
  </aside>

  <section class="admin-dynamic-area">
    <h2 class="title">#<?= (int)$order['id'] ?> • <?= e($order['full_name'] ?? '-') ?> <span class="badge"><?= e(trStatus($order['status'])) ?></span></h2>

    <div class="meta">
      <div class="card">
        <strong>Müşteri</strong><br>
        <div><?= e($order['full_name'] ?? '-') ?></div>
        <div class="muted">Tel: <?= e($order['phone'] ?? '-') ?></div>
        <div class="muted">Oluşturma: <?= e($order['created_at']) ?></div>
      </div>
      <div class="card">
        <strong>Adres</strong><br>
        <div><?= nl2br(e($order['address'] ?? '-')) ?></div>
      </div>
    </div>

    <div class="actions">
      <?php if ($order['status'] !== 'preparing'): ?>
        <button class="edit-btn" onclick="updateOrderStatus(<?= (int)$order['id'] ?>,'preparing')">Hazırlanıyor</button>
      <?php endif; if ($order['status'] !== 'shipped'): ?>
        <button class="edit-btn" onclick="updateOrderStatus(<?= (int)$order['id'] ?>,'shipped')">Kargoya Ver</button>
      <?php endif; if ($order['status'] !== 'active'): ?>
        <button class="edit-btn" onclick="updateOrderStatus(<?= (int)$order['id'] ?>,'active')">Aktif Yap</button>
      <?php endif; ?>
      <button class="delete-btn" onclick="updateOrderStatus(<?= (int)$order['id'] ?>,'cancelled')">İptal</button>
    </div>

    <table class="product-table" id="order-items-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Ürün</th>
          <th>Adet</th>
          <th>Birim Fiyat (₺)</th>
          <th>Ara Toplam (₺)</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($items): foreach ($items as $row): ?>
          <tr>
            <td><?= (int)$row['id'] ?></td>
            <td style="text-align:left">
                <?php
                $img = $row['product_image'] ?? '';
                if ($img) {
                    // 'uploads/...' gibi göreli gelirse başına '/hatter/' ekle
                    $imgPath = (strpos($img, '/') === 0) ? $img : '/hatter/' . ltrim($img, '/');
                ?>
                    <img class="thumb" src="<?= e($imgPath) ?>" alt="">
                <?php } ?>


              <div style="display:inline-block;vertical-align:top;margin-left:.8rem">
                <div style="font-weight:600"><?= e($row['product_name']) ?></div>
                <div class="muted">#<?= (int)$row['product_id'] ?></div>
              </div>
            </td>
            <td><?= (int)$row['qty'] ?></td>
            <td><?= number_format((float)$row['price'], 2, ',', '.') ?></td>
            <td><?= number_format((float)$row['subtotal'], 2, ',', '.') ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5">Kalem bulunamadı.</td></tr>
        <?php endif; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4" style="text-align:right">Toplam:</th>
          <th><?= number_format((float)$total, 2, ',', '.') ?></th>
        </tr>
      </tfoot>
    </table>

    <div class="actions">
      <a class="admin-menu-btn" href="/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/orders.php">← Listeye dön</a>
    </div>
  </section>
</section>

<script>
/* mevcut JSON endpoint: update-order-status.php */
function updateOrderStatus(orderId, newStatus){
  fetch('/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/update-order-status.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id=' + encodeURIComponent(orderId) + '&status=' + encodeURIComponent(newStatus)
  })
  .then(r => r.json())
  .then(j => {
    if (j.ok) { location.reload(); }
    else { alert(j.error || 'Güncellenemedi'); }
  })
  .catch(() => alert('Ağ hatası'));
}
</script>

<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>
</body>
</html>
