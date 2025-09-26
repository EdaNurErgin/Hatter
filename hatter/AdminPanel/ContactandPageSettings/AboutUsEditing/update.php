<?php
declare(strict_types=1);

session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success'=>false,'message'=>'forbidden']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$title     = trim($_POST['title']     ?? '');
$subtitle  = trim($_POST['subtitle']  ?? '');
$content1  = trim($_POST['content1']  ?? '');
$content2  = trim($_POST['content2']  ?? '');
$content3  = trim($_POST['content3']  ?? '');
$id        = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
$current   = trim($_POST['current_image_url'] ?? '');

if ($title === '' || $content1 === '') {
    echo json_encode(['success'=>false,'message'=>'Başlık ve ilk içerik (content1) zorunludur.']);
    exit;
}

// ---- Dosya yükleme (opsiyonel) ----
$imageUrl = $current;
if (!empty($_FILES['image_file']['name'])) {
    $file = $_FILES['image_file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success'=>false,'message'=>'Dosya yükleme hatası: '.$file['error']]);
        exit;
    }
    if ($file['size'] > 2*1024*1024) {
        echo json_encode(['success'=>false,'message'=>'Dosya 2MB sınırını aşıyor.']);
        exit;
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    if (!isset($allowed[$mime])) {
        echo json_encode(['success'=>false,'message'=>'Sadece JPEG/PNG/WebP/GIF kabul edilir.']);
        exit;
    }
    $dir = 'C:/xampp/htdocs/hatter/uploads/about';
    if (!is_dir($dir) && !mkdir($dir,0777,true)) {
        echo json_encode(['success'=>false,'message'=>'Klasör oluşturulamadı.']);
        exit;
    }
    $ext   = $allowed[$mime];
    $fname = 'about_'.date('Ymd_His').'_' . bin2hex(random_bytes(4)).'.'.$ext;
    $dest  = $dir . DIRECTORY_SEPARATOR . $fname;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['success'=>false,'message'=>'Dosya taşınamadı.']);
        exit;
    }
    $imageUrl = '/hatter/uploads/about/' . $fname;
}

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=hatter;charset=utf8mb4','root','');
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    if ($id) {
        $sql = 'UPDATE about_page
                   SET title=:title,
                       subtitle=:subtitle,
                       content1=:content1,
                       content2=:content2,
                       content3=:content3,
                       image_url=:image_url,
                       updated_at=NOW()
                 WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title'     => $title,
            ':subtitle'  => $subtitle,
            ':content1'  => $content1,
            ':content2'  => $content2,
            ':content3'  => $content3,
            ':image_url' => $imageUrl,
            ':id'        => $id
        ]);
    } else {
        $sql = 'INSERT INTO about_page
                  (title, subtitle, content1, content2, content3, image_url, updated_at)
                VALUES
                  (:title, :subtitle, :content1, :content2, :content3, :image_url, NOW())';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title'     => $title,
            ':subtitle'  => $subtitle,
            ':content1'  => $content1,
            ':content2'  => $content2,
            ':content3'  => $content3,
            ':image_url' => $imageUrl
        ]);
    }

    echo json_encode(['success'=>true,'image_url'=>$imageUrl]);
} catch (Throwable $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
