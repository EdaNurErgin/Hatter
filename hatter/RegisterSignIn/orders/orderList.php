<?php
declare(strict_types=1);
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

$userId = $_SESSION["user_id"] ?? null;
if (!$userId) {
    echo "<p>Oturum bulunamadı.</p>";
    exit;
}

/* Siparişleri çek */
$sql = "SELECT id, created_at, total_amount, status
        FROM orders
        WHERE user_id = :uid
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':uid', (int)$userId, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$orders) {
    echo "<p>Henüz siparişiniz yok.</p>";
    exit;
}

/* Basit durum rozeti */
function status_badge(string $s): string {
    $map = [
        'active'    => '#0ea5e9',   // mavi
        'preparing' => '#f59e0b',   // turuncu
        'shipped'   => '#10b981',   // yeşil
        'cancelled' => '#ef4444',   // kırmızı
    ];
    $color = $map[$s] ?? '#6b7280';
    $label = [
        'active'    => 'Alındı',
        'preparing' => 'Hazırlanıyor',
        'shipped'   => 'Kargolandı',
        'cancelled' => 'İptal'
    ][$s] ?? ucfirst($s);
    return "<span style='display:inline-block;padding:4px 8px;border-radius:999px;background:$color;color:#fff;font-size:.85rem;'>$label</span>";
}

echo "<table style='width:100%; border-collapse:collapse; text-align:left; font-family:system-ui,Segoe UI,Roboto,Arial; '>
<thead >
<tr style='background:#f9f9f9;'>
  <th style='border-bottom:1px solid #ddd; padding:12px;'>#</th>
  <th style='border-bottom:1px solid #ddd; padding:12px;'>Tarih</th>
  <th style='border-bottom:1px solid #ddd; padding:12px;'>Tutar</th>
  <th style='border-bottom:1px solid #ddd; padding:12px;'>Durum</th>
</tr>
</thead><tbody>";

foreach ($orders as $o) {
    $id    = (int)$o['id'];
    $date  = date("d.m.Y H:i", strtotime($o['created_at']));
    $total = number_format((float)$o['total_amount'], 2, ',', '.')." TL";
    $st    = status_badge((string)$o['status']);

    echo "<tr style='border-bottom:1px solid #eee;'>
            <td style='padding:12px;'>$id</td>
            <td style='padding:12px;'>$date</td>
            <td style='padding:12px;'>$total</td>
            <td style='padding:12px;'>$st</td>
          </tr>";
}
echo "</tbody></table>";
