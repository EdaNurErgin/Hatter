<?php
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(["error" => "Ürün bulunamadı"]);
    }
} else {
    echo json_encode(["error" => "ID eksik"]);
}
?>
