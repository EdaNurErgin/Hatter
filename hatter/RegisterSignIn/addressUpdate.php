<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $content = trim($_POST["content"]);

    if (empty($id) || empty($content)) {
        echo "eksik veri";
        exit;
    }

    $stmt = $conn->prepare("UPDATE address SET content = ? WHERE id = ? AND user_id = ?");
    $basarili = $stmt->execute([$content, $id, $_SESSION["user_id"]]);

    echo $basarili ? "guncellendi" : "hata";
}
?>
