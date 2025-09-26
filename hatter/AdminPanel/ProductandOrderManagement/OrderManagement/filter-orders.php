<?php
declare(strict_types=1);
session_start();

/* --- Admin koruması --- */
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    echo 'Yetkisiz erişim';
    exit;
}

/* --- DB bağlantısı --- */
$pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* --- Parametreler --- */
$allowed = ['active','preparing','shipped','cancelled'];
$status  = isset($_GET['status']) && in_array($_GET['status'], $allowed, true) ? $_GET['status'] : 'active';

$page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit   = 10;
$offset  = ($page - 1) * $limit;

/* --- Toplam kayıt --- */
$stmtCnt = $pdo->prepare("SELECT COUNT(*) 
                          FROM orders o 
                          WHERE o.status = :status");
$stmtCnt->execute([':status' => $status]);
$total = (int)$stmtCnt->fetchColumn();
$pages = (int)ceil($total / $limit);

/* --- Kayıtları çek --- */
$sql = "
SELECT 
  o.id,
  o.full_name,
  o.phone,
  o.address,
  o.total_amount,
  o.created_at,
  o.status,
  COALESCE(SUM(oi.qty),0)        AS total_qty,
  COALESCE(SUM(oi.subtotal),0.0) AS items_total
FROM orders o
LEFT JOIN order_items oi ON oi.order_id = o.id
WHERE o.status = :status
GROUP BY o.id
ORDER BY o.created_at DESC
LIMIT :offset, :limit
";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':status', $status, PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);



/* --- Yardımcılar --- */
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function trStatus(string $s): string {
    return [
        'active'    => 'Aktif',
        'preparing' => 'Hazırlanıyor',
        'shipped'   => 'Kargoda',
        'cancelled' => 'İptal'
    ][$s] ?? $s;
}


if (!$rows) {
    ?>
    <div class="empty-wrapper">

    <div class="empty-state">
        <i class="fa fa-box-open"></i>
        <h2>There are no orders in this category</h2>
        <p>When new orders arrive, you will see them here.</p>
        <a href="javascript:location.reload()" class="btn btn--primary">Refresh</a>
    </div>
    </div>
    <?php
    exit;
}

?>

<table class="product-table" id="order-table">
  <thead>
    <tr>
      <th>#</th>
      <th>Müşteri</th>
      <th>Telefon</th>
      <th>Adres</th>
      <th>Ürün Adedi</th>
      <th>Tutar (₺)</th>
      <th>Oluşturma</th>
      <th>Durum</th>
      <th>İşlem</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><?= e($r['full_name'] ?? '-') ?></td>
      <td><?= e($r['phone'] ?? '-') ?></td>
      <td title="<?= e($r['address'] ?? '-') ?>">
        <?= mb_strimwidth($r['address'] ?? '-', 0, 40, '...', 'UTF-8') ?>
      </td>
      <td><?= (int)$r['total_qty'] ?></td>
      <td><?= number_format((float)($r['total_amount'] ?: $r['items_total']), 2, ',', '.') ?></td>
      <td><?= e($r['created_at']) ?></td>
      <td><span class="badge"><?= e(trStatus($r['status'])) ?></span></td>
      <td>
        <a class="edit-btn" href="/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/order-details.php?id=<?= (int)$r['id'] ?>">Detay</a>
        <!-- <?php if ($r['status'] !== 'preparing'): ?>
          <button class="edit-btn" onclick="updateOrderStatus(<?= (int)$r['id'] ?>,'preparing')" >Hazırlanıyor</button>
        <?php endif; ?>
        <?php if ($r['status'] !== 'shipped'): ?>
          <button class="edit-btn" onclick="updateOrderStatus(<?= (int)$r['id'] ?>,'shipped')">Kargoya Ver</button>
        <?php endif; ?>
        <?php if ($r['status'] !== 'active'): ?>
          <button class="edit-btn" onclick="updateOrderStatus(<?= (int)$r['id'] ?>,'active')">Aktif Yap</button>
        <?php endif; ?> -->
        <!-- <button class="delete-btn" onclick="updateOrderStatus(<?= (int)$r['id'] ?>,'cancelled')">İptal</button> -->
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php if ($pages > 1): ?>
<div style="margin-top:1rem; display:flex; gap:.5rem; justify-content:flex-end;">
  <?php for ($i=1; $i <= $pages; $i++): 
    $isActive = $i === $page;
  ?>
    <button class="admin-menu-btn" style="padding:.6rem 1rem;<?= $isActive ? 'background:#2e8b57;color:#fff' : '' ?>"
      onclick="filterOrders('<?= e($status) ?>', <?= $i ?>)">
      <?= $i ?>
    </button>
  <?php endfor; ?>
</div>
<?php endif; ?>

<script>
/* durum güncelleme */

function updateOrderStatus(orderId, newStatus){
  const body = new URLSearchParams();
  body.set('id', orderId);
  body.set('status', newStatus);

  fetch('/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/update-order-status.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: body.toString()
  })
  .then(r => r.json())
  .then(j => {
    if (j.ok) {
      // Üst sayfada filterOrders varsa onu çağır, yoksa sayfayı yenile
      if (typeof filterOrders === 'function') {
        const current = '<?= e($status) ?>';
        const p = <?= (int)$page ?>;
        filterOrders(current, p);
      } else {
        location.reload();
      }
    } else {
      alert(j.error || 'Güncellenemedi');
    }
  })
  .catch(() => alert('Ağ hatası'));
}


</script>
