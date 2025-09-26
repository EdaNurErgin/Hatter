<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// ADRES EKLEME
// Sadece POST isteğiyle çalış
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION["user_id"] ?? null;
    $content = trim($_POST["content"] ?? '');

    if ($userId && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO address (content, user_id) VALUES (?, ?)");
        $stmt->execute([$content, $userId]);

        // AJAX çağrısı varsa "başarılı" cevabı gönder
        echo "eklendi";
    } else {
        echo "eksik veri";
    }
} else {
    echo "geçersiz istek";
}



$userId = $_SESSION["user_id"] ?? null;

if (!$userId) {
    echo "<p>Oturum bulunamadı.</p>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM address WHERE user_id = ?");
$stmt->execute([$userId]);
$adresler = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($adresler)) {
    echo "<p>Henüz kayıtlı adresiniz yok.</p>";
    exit;
}

foreach ($adresler as $adres) {
    echo '<div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; border-radius: 5px;">';
    echo '<p>' . htmlspecialchars($adres["content"]) . '</p>';
    echo '<form method="POST" onsubmit="return adresSil(this);">';
    echo '<input type="hidden" name="adres_id" value="' . $adres["id"] . '">';
    echo '<button type="submit" class="btn" style="background: #e74c3c;">Sil</button>';
    echo '</form>';
    echo '</div>';
}


// ADRES SILME
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_SESSION["user_id"] ?? null;
    $adresId = $_POST["adres_id"] ?? null;

    if ($userId && $adresId) {
        $stmt = $conn->prepare("DELETE FROM address WHERE id = ? AND user_id = ?");
        $stmt->execute([$adresId, $userId]);
        echo "silindi";
    } else {
        echo "eksik veri";
    }
} else {
    echo "geçersiz istek";
}

?>
