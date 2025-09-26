<?php
// Oturum (gerekirse korumalı alanlar/mesajlar için)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// Post -> DB'ye kaydet
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Alanları al + kırp
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $phone   = trim($_POST['phone']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // 2) Basit doğrulamalar (şemanıza uygun)
    if ($name === '' || mb_strlen($name) > 255) {
        $errors[] = 'İsim zorunlu ve 255 karakteri geçmemeli.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
        $errors[] = 'Geçerli bir e-posta girin (en fazla 255 karakter).';
    }

    if ($phone !== '') {
        if (mb_strlen($phone) > 20) {
            $errors[] = 'Telefon en fazla 20 karakter olmalı.';
        }
    } else {
        $phone = null; // sütun NULL kabul ediyor
    }

    if ($message === '') {
        $errors[] = 'Mesaj alanı boş olamaz.';
    }

    // 3) Kaydet
    if (!$errors) {
        try {
            $sql = "INSERT INTO contact_messages (name, email, phone, message)
                    VALUES (:name, :email, :phone, :message)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':name',    $name,    PDO::PARAM_STR);
            $stmt->bindValue(':email',   $email,   PDO::PARAM_STR);
            // phone null olabilir
            if ($phone === null) {
                $stmt->bindValue(':phone', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
            }
            $stmt->bindValue(':message', $message, PDO::PARAM_STR);

            $stmt->execute();
            // Başarılı: PRG (Post/Redirect/Get) ile yenilemede tekrar kaydı önle
            header("Location: " . $_SERVER['PHP_SELF'] . "?ok=1");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Kayıt hatası: " . $e->getMessage();
        }
    }
}

// Başarı mesajı
if (isset($_GET['ok']) && $_GET['ok'] === '1') {
    $success = true;
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

    <style>
   
/* Yalnızca message textarea'sını input ile aynı görünüme getirir */
.contact form .inputBox textarea{
  background: transparent !important;  /* beyaz kutuyu kaldırır */
  color: inherit;                       /* mevcut metin rengini kullan */
  border: 1px solid rgba(255,255,255,.25); /* input ile aynı sınır */
  width: 100%;
  min-height: 110px;
  padding: .9rem 1rem .9rem 2.4rem;     /* ikonla çakışmasın, input ile aynı */
  outline: none;
  box-shadow: none !important;          /* tema gölgesi varsa sil */
  -webkit-appearance: none;
  appearance: none;
}

/* Bazı temalarda ikonun arkasına beyaz veriliyor olabilir: sıfırla */
.contact form .inputBox i{
  background: transparent !important;
}


    </style>
</head>
<body>

<?php include 'C:/xampp/htdocs/hatter/root/header.php'; ?>

<section class="contact">
    <h1 class="heading">contact<span>us</span></h1>

    <div class="row">
        <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d188.2300383636035!2d29.025817667459098!3d40.98847399999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14cab930550a56af%3A0x3046e4df3fadf0e5!2sAnatolia%20Toastmasters!5e0!3m2!1str!2str!4v1740912564973!5m2!1str!2str" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <h3>get in touch</h3>

            <?php if ($success): ?>
                <div class="alert success">Mesajınız başarıyla gönderildi. Teşekkürler!</div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <?php foreach ($errors as $e): ?>
                        <p><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="inputBox">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="name" required maxlength="255"
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>

            <div class="inputBox">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="e-mail" required maxlength="255"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>

            <div class="inputBox">
                <i class="fas fa-phone"></i>
                <input type="text" name="phone" placeholder="phone" maxlength="20"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8') : '' ?>">
            </div>

            <div class="inputBox">
                <i class="fas fa-comment"></i>
                <textarea name="message" placeholder="message" required rows="4"><?php
                    echo isset($_POST['message']) ? htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8') : '';
                ?></textarea>
            </div>

            <input type="submit" class="btn" value="contact now">
        </form>
    </div>
</section>

<?php include 'C:/xampp/htdocs/hatter/root/footer.php'; ?>
</body>
</html>
