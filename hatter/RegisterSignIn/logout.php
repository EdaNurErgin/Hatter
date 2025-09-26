<?php
session_start();

// Tüm oturum verilerini temizle
$_SESSION = [];

// Oturumu tamamen sonlandır
session_destroy();

// Giriş ekranına veya anasayfaya yönlendir
header("Location: http://localhost/hatter/index.php");
exit;
?>
