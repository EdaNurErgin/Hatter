<?php
// /hatter/cart/remove.php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$r  = !empty($_GET['r']) ? $_GET['r'] : '/hatter/Products/index.php';

if ($id > 0 && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

// boşsa komple sepeti de kaldırabilirsin (opsiyonel)
if (empty($_SESSION['cart'])) { unset($_SESSION['cart']); }

header('Location: ' . $r);
exit;
