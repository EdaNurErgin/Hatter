<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
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

    
<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>
    <!-- home section start -->
   <section class="home">
    <div class="content">
    <h3>Welcome to the Admin Panel</h3>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. 
        Facere quisquam porro temporibus.
         Voluptatibus et quae, 
         error mollitia neque nulla aliquid eius nam reprehenderit, 
         saepe quo fugit debitis facere eveniet deserunt!</p>
    <a href="#" class="btn"> See New Orders </a>
   </div>
   </section>
<!-- home section end -->


<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>



</body>
</html>