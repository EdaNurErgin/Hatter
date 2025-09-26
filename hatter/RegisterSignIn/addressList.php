<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";



// ADRES LİSTELEME 
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
    echo '
    <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px;">
      <p>' . htmlspecialchars($adres["content"]) . '</p>

      <form onsubmit="return adresSil(this)" style="display:inline;">
        <input type="hidden" name="id" value="' . $adres["id"] . '">
        <button type="submit" class="btn" style="background:red; font-size:12px;">Sil</button>
      </form>

      <button class="btn" style="background:rgba(0, 128, 0, 0.952); font-size:12px;" 
        onclick="adresGuncelleFormunuAc(\'' . htmlspecialchars(addslashes($adres["content"])) . '\', \'' . $adres["id"] . '\')">
        Güncelle
      </button>
    </div>';
}



?>
