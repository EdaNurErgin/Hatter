<?php
declare(strict_types=1); // 1. SATIRDA!
session_start();

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    exit('Yetkisiz erişim');
}

require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";
header('Content-Type: text/html; charset=UTF-8');

// Hata görmeniz gerekirse geçici açabilirsiniz:
// ini_set('display_errors', '1'); error_reporting(E_ALL);

$sql = "SELECT id, name, created_at, email, phone, message
        FROM contact_messages
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sadece tablo HTML'i dönüyoruz (ekstra <html> <body> yok!)
?>
<table class="product-table">
  <thead>
    <tr>
      <th>İsim</th>
      <th>Gönderim Tarihi</th>
      <th>Email</th>
      <th>Telefon</th>
      <th>Mesaj</th>
      <th>İşlem</th>
    </tr>
  </thead>
  <tbody>
  <?php if ($rows && count($rows) > 0): ?>
    <?php foreach ($rows as $row): 
      $id      = (int)($row['id'] ?? 0);
      $name    = htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8');
      $email   = htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8');
      $phone   = htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8');
      $created = htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8');
      // data-attribute içinde çift tırnakları kaçır:
      $msgAttr = htmlspecialchars($row['message'] ?? '', ENT_QUOTES, 'UTF-8');
    ?>
      <tr>
        <td><?= $name ?></td>
        <td><?= $created ?></td>
        <td><?= $email ?></td>
        <td><?= $phone ?></td>
        <td><button class="read-btn" data-content="<?= $msgAttr ?>">📖 Read</button></td>
        <td><button onclick="deletePost(<?= $id ?>)">🗑️ Sil</button></td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
      <tr><td colspan="6">Mesaj bulunamadı.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
