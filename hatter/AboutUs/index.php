<?php
session_start();

include 'C:/xampp/htdocs/hatter/sqlBaglanti/db.php';

try {
    // Tek kaydı al
    $stmt = $conn->query("SELECT title, subtitle, content1, content2, content3, image_url, updated_at
                         FROM about_page
                         ORDER BY id
                         LIMIT 1");
    $about = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'title' => 'About Us',
        'subtitle' => '',
        'content1' => '',
        'content2' => '',
        'content3' => '',
        'image_url' => '/hatter/images/kl.jpg',
        'updated_at' => null
    ];

} catch (Throwable $e) {
    // Hata durumunda fallback veriler
    $about = [
        'title' => 'About Us',
        'subtitle' => '',
        'content1' => 'İçerik yüklenemedi.',
        'content2' => '',
        'content3' => '',
        'image_url' => '/hatter/images/kl.jpg',
        'updated_at' => null
    ];
}
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
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

<section class="about">
    <h1 class="heading"> <?php echo e('about'); ?> <span> <?php echo e('us'); ?> </span></h1>

    <div class="row">
        <div class="image">
            <img src="<?php echo e($about['image_url'] ?: '/hatter/images/kl.jpg'); ?>" alt="about"/>
        </div>

        <div class="content">
            <h3><?php echo e($about['title'] ?: 'About Us'); ?></h3>
            <?php if(!empty($about['subtitle'])): ?>
                <h4 style="margin-top:.5rem;color:#777;"><?php echo e($about['subtitle']); ?></h4>
            <?php endif; ?>

            <?php if(!empty($about['content1'])): ?>
                <p><?php echo nl2br(e($about['content1'])); ?></p>
            <?php endif; ?>
            <?php if(!empty($about['content2'])): ?>
                <p><?php echo nl2br(e($about['content2'])); ?></p>
            <?php endif; ?>
            <?php if(!empty($about['content3'])): ?>
                <p><?php echo nl2br(e($about['content3'])); ?></p>
            <?php endif; ?>

            <a href="#" class="btn">learn more</a>

            <?php if(!empty($about['updated_at'])): ?>
                <small style="display:block;margin-top:1rem;color:#888;">
                    <!-- Last update: <?php echo e($about['updated_at']); ?> -->
                </small>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'C:/xampp/htdocs/hatter/root/footer.php'; ?>
</body>
</html>
