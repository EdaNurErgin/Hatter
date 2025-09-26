
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: /hatter/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hatter</title>
    <link rel="stylesheet" href="/hatter/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<?php include 'C:/xampp/htdocs/hatter/root/header.php'; ?>


<div class="outer-container">
<div class="tab-container">
    <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab('login')">Giriş Yap</button>
        <button class="tab-btn" onclick="openTab('register')">Üye Ol</button>
    </div>

    <div class="tab-content-wrapper">
        <div class="tab-content active" id="login">

            <form action="login.php" method="post">
                <input type="email" name="email" placeholder="E-Posta">
                <input type="password" name="password" placeholder="Şifre">
                <a href="#">Password Forget</a>
                <button class="submit-btn" type="submit">LOG IN</button>
            </form>
        </div>

        <div class="tab-content" id="register">

            <form action="register.php" method="post" id="ty">
                <input type="text" name="full_name" placeholder="Ad Soyad">
                <input type="email" name="email" placeholder="E-Posta">
                <input type="password" name="password" placeholder="Şifre">
                <button class="submit-btn" type="submit">SIGN UP</button>
            </form>

        </div>
    </div>
</div>
</div>

<script>
function openTab(tabId) {
    // Tüm içerikleri gizle
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');

    // Tüm butonlardan 'active' class'ını kaldır
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

    // Seçilen içeriği göster ve butonu active yap
    document.getElementById(tabId).style.display = 'flex';
    event.target.classList.add('active');
}
</script>



<div id="snackbar"></div>
<script>
function showSnackbar(message) {
    const snackbar = document.getElementById("snackbar");
    snackbar.textContent = message;
    snackbar.classList.add("show");
    setTimeout(() => {
        snackbar.classList.remove("show");
    }, 3000);
}
</script>
<?php if (isset($_SESSION["message"])): ?>
  <script>showSnackbar("<?= $_SESSION['message'] ?>");</script>
  <?php unset($_SESSION["message"]); ?>
<?php endif; ?>



<?php include 'C:/xampp/htdocs/hatter/root/footer.php'; ?>


</body>
</html>