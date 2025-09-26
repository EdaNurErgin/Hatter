<?php
declare(strict_types=1);
session_start();

/* --- Admin kontrolü --- */
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
  http_response_code(403);
  exit('Yetkisiz');
}

/* --- DB --- */
$pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/* --- Parametre --- */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); exit('Geçersiz istek'); }

/* --- Sipariş başlık --- */
$h = $pdo->prepare("SELECT id, full_name, phone, address, total_amount, status, created_at
                    FROM orders WHERE id=:id");
$h->execute([':id'=>$id]);
$o = $h->fetch(PDO::FETCH_ASSOC);
if (!$o) { exit('Sipariş bulunamadı'); }

/* --- Kalemler --- */
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

/* --- Yardımcılar --- */
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function trStatus(string $s): string {
  return ['active'=>'Aktif','preparing'=>'Hazırlanıyor','shipped'=>'Kargoda','cancelled'=>'İptal'][$s] ?? $s;
}
function statusClass(string $s): string {
  return [
    'active'    => 'badge badge--info',
    'preparing' => 'badge badge--warn',
    'shipped'   => 'badge badge--ok',
    'cancelled' => 'badge badge--danger'
  ][$s] ?? 'badge';
}

/* --- Toplamlar --- */
$items_total = 0.0; $qty_total = 0;
foreach ($items as $r) { $items_total += (float)$r['subtotal']; $qty_total += (int)$r['qty']; }
$total = ($o['total_amount'] !== null && $o['total_amount'] !== '') ? (float)$o['total_amount'] : $items_total;

/* --- Adım çubuğu --- */
$steps = ['active','preparing','shipped'];
$stepIndex = in_array($o['status'], $steps, true) ? array_search($o['status'], $steps, true) : -1;

/* --- CSRF --- */
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Sipariş #<?= (int)$o['id'] ?> — Hatter</title>
  <link rel="stylesheet" href="/hatter/styles/style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <style>
    :root{ --header-h: 80px; } /* Sabit header yüksekliğini burada ayarla */

    /* Sayfa arka planı */
    

    /* Dış kapsayıcı: sayfayı ortala ve üst boşluğu header'a göre ayarla */
    .od-container{
      max-width: min(1200px, 96vw);
      margin: 0 auto;
      padding: calc(var(--header-h) + -40px) 20px 28px;
    }

    /* Tek yüzey (arka planlı kart) */
    .od-surface{
      background:#fff;
      border-radius: 18px;
      box-shadow: 0 12px 32px rgba(0,0,0,.08);
      padding: clamp(16px, 2.2vw, 24px);
    }

    /* İç tipografi/ölçek */
    .od-page, .od-page table, .od-page .card{
      font-size: clamp(15px, 1.25vw, 17px);
      line-height: 1.5;
    }

    .od-header{display:flex;flex-wrap:wrap;align-items:center;gap:.75rem;justify-content:space-between;margin-bottom:1rem}
    .od-left{display:flex;align-items:center;gap:.75rem}
    .od-actions{display:flex;gap:.6rem;flex-wrap:wrap}

    .od-page h2{ font-size: clamp(22px, 2.2vw, 30px); margin:0; }
    .od-page h3{ font-size: clamp(18px, 1.7vw, 22px); margin:0 0 .5rem; }
    .muted{color:#6b7280;font-size: clamp(13px, 1vw, 14px);}

    .badge{display:inline-block;padding:.45rem .75rem;border-radius:999px;background:#eee;font-weight:700}
    .badge--info{background:#e6f2ff;color:#0b63b1}
    .badge--warn{background:#fff6e6;color:#b15c0b}
    .badge--ok{background:#e9f8ee;color:#2e7d32}
    .badge--danger{background:#ffe8e6;color:#b11a0b}

    .btn{
      padding: clamp(.6rem, 1.2vw, .8rem) clamp(.9rem, 1.6vw, 1.1rem);
      font-size: clamp(14px, 1.2vw, 16px);
      border-radius: .7rem;
      border:1px solid #d1d5db; background:#f9fafb; cursor:pointer;
    }
    .btn:hover{ background:#f3f4f6 }
    .btn--primary{ background:#2e8b57; color:#fff; border-color:#2e8b57 }
    .btn--danger{ background:#ef4444; color:#fff; border-color:#ef4444 }
    .btn--ghost{ background:#fff; }

    .od-steps{ display:flex; gap:.6rem; margin:.25rem 0 1rem; }
    .od-step{ height:8px; border-radius:999px; background:#ececec; flex:1; }
    .od-step.on{ background:#2e8b57; }
    .od-step.cancel{ background:#ffe1df; }

    .od-grid{ display:grid; grid-template-columns:1.2fr .8fr; gap: clamp(12px, 2vw, 20px); }
    @media (max-width: 900px){ .od-grid{ grid-template-columns:1fr; } }

    .card{ background:#fff; border-radius:14px; box-shadow:0 8px 24px rgba(0,0,0,.06); padding: clamp(14px, 2vw, 18px); }
    .kv{display:grid;grid-template-columns:auto 1fr;gap:.25rem .75rem}
    .kv dt{color:#6b7280}
    .kv dd{font-weight:600}

    .table-wrap{ overflow:auto; }
    .product-table{ width:100%; border-collapse:collapse; }
    .product-table th, .product-table td{
      padding:.7rem .75rem; border-bottom:1px solid #eee; text-align:center; white-space:nowrap;
    }
    .product-table thead th{ background:#f8fafc; font-weight:700; }
    .product-table td:nth-child(2){ text-align:left; white-space:normal; }
    .thumb{ width:56px; height:56px; border-radius:8px; margin-right:.6rem; object-fit:cover; vertical-align:middle; }
    li{
      list-style: none;
      
     
    }

    /* Liste sayfasından kalan layout sınıflarını devre dışı bırak (güvenli tarafta kalmak için) */
    .bg, .admin-panel-layout, .panel-section { all: unset; }
  </style>
</head>
<body>

<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>

<main class="od-container">
  <div class="od-surface">
    <div class="od-page">

      <div class="od-header">

        <div class="od-left">
          <ul>
            <li >
          <a class="btn btn--ghost" href="javascript:history.back()"><i class="fa fa-arrow-left"></i> Geri dön</a>
          

          <h2 style="padding-top:20px">Sipariş #<?= (int)$o['id'] ?></h2>
          <span class="<?= e(statusClass($o['status'])) ?>"><?= e(trStatus($o['status'])) ?></span>
        </div>
        <div class="od-actions">
          <?php if ($o['status']!=='preparing' && $o['status']!=='cancelled'): ?>
            <button class="btn" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'preparing')">Hazırlanıyor</button>
          <?php endif; if($o['status']!=='shipped' && $o['status']!=='cancelled'): ?>
            <button class="btn btn--primary" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'shipped')">Kargoya Ver</button>
          <?php endif; if($o['status']!=='active' && $o['status']!=='cancelled'): ?>
            <button class="btn" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'active')">Aktif Yap</button>
          <?php endif; if($o['status']!=='cancelled'): ?>
            <button class="btn btn--danger" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'cancelled')">İptal</button>
          <?php endif; ?>
          <button class="btn" onclick="window.print()"><i class="fa fa-print"></i> Yazdır</button>

        </div>
          </li>
        </ul>
      </div>

      <!-- Adım çubuğu -->
      <div class="od-steps" aria-label="Sipariş adımları">
        <?php if ($o['status']==='cancelled'): ?>
          <div class="od-step cancel"></div>
          <div class="od-step cancel"></div>
          <div class="od-step cancel"></div>
        <?php else: ?>
          <div class="od-step <?= $stepIndex>=0 ? 'on':'' ?>"></div>
          <div class="od-step <?= $stepIndex>=1 ? 'on':'' ?>"></div>
          <div class="od-step <?= $stepIndex>=2 ? 'on':'' ?>"></div>
        <?php endif; ?>
      </div>

      <div class="od-grid" style="margin-top:1rem;">
        <!-- Sol: Müşteri + Adres -->
        <div class="card">
          <h3>Müşteri Bilgileri</h3>
          <dl class="kv">
            <dt>Ad Soyad</dt><dd><?= e($o['full_name'] ?? '-') ?></dd>
            <dt>Telefon</dt><dd><?= e($o['phone'] ?? '-') ?></dd>
            <dt>Oluşturma</dt><dd><?= e(date('d.m.Y H:i', strtotime((string)$o['created_at']))) ?></dd>
          </dl>
          <hr style="border:none;border-top:1px solid #eee;margin:1rem 0"/>
          <h3>Adres</h3>
          <div class="muted"><?= nl2br(e($o['address'] ?? '-')) ?></div>
        </div>

        <!-- Sağ: Özet -->
        <div class="card">
          <h3>Sipariş Özeti</h3>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin-top:.5rem">
            <div style="padding:.75rem;border-radius:.75rem;background:#f8fafc;text-align:center">
              <span class="muted">Ürün Adedi</span>
              <strong style="display:block;font-size:1.1rem"><?= (int)$qty_total ?></strong>
            </div>
            <div style="padding:.75rem;border-radius:.75rem;background:#f8fafc;text-align:center">
              <span class="muted">Ara Toplam</span>
              <strong style="display:block;font-size:1.1rem"><?= number_format((float)$items_total, 2, ',', '.') ?> ₺</strong>
            </div>
            <div style="padding:.75rem;border-radius:.75rem;background:#f8fafc;text-align:center">
              <span class="muted">Toplam</span>
              <strong style="display:block;font-size:1.1rem"><?= number_format((float)$total, 2, ',', '.') ?> ₺</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Ürünler -->
      <div class="card" style="margin-top:1rem;">
        <h3>Ürünler</h3>
        <div class="table-wrap">
          <table class="product-table">
            <thead>
              <tr>
                <th>#</th>
                <th style="text-align:left">Ürün</th>
                <th>Adet</th>
                <th>Birim (₺)</th>
                <th>Ara Toplam (₺)</th>
              </tr>
            </thead>
            <tbody>
            <?php if ($items): foreach ($items as $r):
              $img = $r['product_image'] ?? '';
              $imgPath = $img ? ((strpos($img,'/')===0)?$img:'/hatter/'.ltrim($img,'/')) : '';
            ?>
              <tr>
                <td><?= (int)$r['id'] ?></td>
                <td style="text-align:left">
                  <?php if ($imgPath): ?><img class="thumb" src="<?= e($imgPath) ?>" alt=""><?php endif; ?>
                  <strong><?= e($r['product_name']) ?></strong>
                  <span class="muted"> — #<?= (int)$r['product_id'] ?></span>
                </td>
                <td><?= (int)$r['qty'] ?></td>
                <td><?= number_format((float)$r['price'], 2, ',', '.') ?></td>
                <td><?= number_format((float)$r['subtotal'], 2, ',', '.') ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="5">Kalem yok.</td></tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" style="text-align:right">Toplam:</th>
                <th><?= number_format((float)$total, 2, ',', '.') ?></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Aksiyonlar -->
      <!-- <div class="od-actions" style="margin-top:.9rem">
        <?php if ($o['status']!=='preparing' && $o['status']!=='cancelled'): ?>
          <button class="btn" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'preparing')">Hazırlanıyor</button>
        <?php endif; if($o['status']!=='shipped' && $o['status']!=='cancelled'): ?>
          <button class="btn btn--primary" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'shipped')">Kargoya Ver</button>
        <?php endif; if($o['status']!=='active' && $o['status']!=='cancelled'): ?>
          <button class="btn" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'active')">Aktif Yap</button>
        <?php endif; if($o['status']!=='cancelled'): ?>
          <button class="btn btn--danger" onclick="updateOrderStatus(<?= (int)$o['id'] ?>,'cancelled')">İptal</button>
        <?php endif; ?>
      </div> -->

    </div>
  </div>
</main>

<script>
function updateOrderStatus(orderId, newStatus){
  const body = new URLSearchParams();
  body.set('id', orderId);
  body.set('status', newStatus);
  body.set('csrf', '<?= $csrf ?>');

  fetch('/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/update-order-status.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: body.toString()
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
