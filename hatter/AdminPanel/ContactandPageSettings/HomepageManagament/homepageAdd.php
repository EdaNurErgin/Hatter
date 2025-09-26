<?php
session_start();

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'forbidden']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

/* === Proje yolları ===
   Bu dosya şu klasörde varsayılıyor:
   /hatter/AdminPanel/ContactandPageSettings/Homepage/homepageAdd.php
   Yollar farklıysa $UPLOAD_DIR ve $PUBLIC_PREFIX’i kendi yapına göre değiştir.
*/
// $UPLOAD_DIR   = __DIR__ . '/uploads/homepage/'; // fiziksel klasör
// $PUBLIC_PREFIX= '/hatter/AdminPanel/ContactandPageSettings/Homepage/uploads/homepage/'; // web’den erişilecek yol

// homepageAdd.php
$UPLOAD_DIR    = __DIR__ . '/uploads/homepage/';
$PUBLIC_PREFIX = '/hatter/AdminPanel/ContactandPageSettings/HomepageManagament/uploads/homepage/';


if (!is_dir($UPLOAD_DIR)) { @mkdir($UPLOAD_DIR, 0777, true); }

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Form alanları
    $category_name = trim($_POST['category_name'] ?? '');
    $sort_order    = isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0;
    $is_active     = (isset($_POST['is_active']) && $_POST['is_active'] === '1') ? 1 : 0;

    if ($category_name === '') {
        throw new RuntimeException('Kategori adı zorunlu.');
    }

    // Görsel (opsiyonel)
    $image_url = null;
    if (!empty($_FILES['image_file']['name'])) {
        if ($_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Görsel yüklenemedi (kod: ' . $_FILES['image_file']['error'] . ').');
        }
        if ($_FILES['image_file']['size'] > 5 * 1024 * 1024) {
            throw new RuntimeException('Görsel 5MB’ı aşamaz.');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($_FILES['image_file']['tmp_name']);
        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($extMap[$mime])) {
            throw new RuntimeException('Sadece JPG, PNG veya WEBP yükleyin.');
        }
        $ext = $extMap[$mime];

        $safeBase = preg_replace('~[^a-zA-Z0-9_-]~', '_', pathinfo($_FILES['image_file']['name'], PATHINFO_FILENAME));
        $filename = $safeBase . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;

        $target = $UPLOAD_DIR . $filename;
        if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $target)) {
            throw new RuntimeException('Görsel taşınamadı.');
        }

        $image_url = $PUBLIC_PREFIX . $filename;
    }

    // Insert
    $sql = "INSERT INTO `homepage` (category_name, image_url, sort_order, is_active)
            VALUES (:category_name, :image_url, :sort_order, :is_active)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':category_name' => $category_name,
        ':image_url'     => $image_url,
        ':sort_order'    => $sort_order,
        ':is_active'     => $is_active,
    ]);

    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
