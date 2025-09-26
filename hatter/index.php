<?php
session_start();
?>

<?php
// db bağlantını dahil et
include 'sqlBaglanti/db.php';
?>
<?php
// homepage tablosundan aktif kayıtları sıraya göre al
$stmt = $conn->query("SELECT category_name, image_url FROM homepage WHERE is_active = 1 ORDER BY sort_order ASC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Hatter</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <!-- header section start -->
    <?php include 'C:/xampp/htdocs/hatter/root/header.php'; ?>
 <!-- header section end -->
  <!-- home section start -->
   <section class="home">
    <div class="content">
    <h3>Address of the Hat</h3>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. 
        Facere quisquam porro temporibus.
         Voluptatibus et quae, 
         error mollitia neque nulla aliquid eius nam reprehenderit, 
         saepe quo fugit debitis facere eveniet deserunt!</p>
    <a href="#" class="btn">Order Now</a>
   </div>
   </section>
<!-- home section end -->
 <!-- menu section start -->

  <section class="menu" id="menu">
    <h1 class="heading">our <span> menu</span></h1>
    <div class="box-container">
        <?php foreach ($items as $item): ?>
            <div class="box">
                <div class="box-head">
                    <!-- Veritabanındaki image_url -->
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['category_name']) ?>" />
                    <span class="menu-category"><?= htmlspecialchars($item['category_name']) ?></span>
                    <h3><?= htmlspecialchars($item['category_name']) ?></h3>
                    
                </div>
                <div class="box-bottom">
                    <!-- <a href="#" class="btn">go to look</a> -->
                     <div class="box-bottom">
                        <a href="/hatter/Products/index.php?category=<?= urlencode($item['category_name']) ?>" class="btn">go to look</a>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<!-- menu section end -->

 <!-- footer start -->
  <?php include 'C:/xampp/htdocs/hatter/root/footer.php'; ?>

 <!-- foooter end -->

<script src="js/script.js"></script>
</body>
</html>