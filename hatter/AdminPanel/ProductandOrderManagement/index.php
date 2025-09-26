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
    <style>
    .bg{
            
        min-height: 100vh;
    
        background: url(/hatter/images/n.jpg) no-repeat;
        background-size: cover;
        background-position: center;
        margin-top: -14.5rem;
        display: grid;
        align-items: center; 
   }
        

    </style>
</head>
<body>

    
<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>
<div class="bg">
<section  class="admin-feature-section">
  <div class="admin-feature-container">
    <div class="admin-feature-box">
      <h3>Product Management</h3>
      <a href="/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/index.php" class="admin-feature-btn">Open Panel</a>
    </div>
    <div class="admin-feature-box">
      <h3>Order Management</h3>
      <a href="/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/index.php" class="admin-feature-btn">Open Panel</a>
    </div>
    <!-- <div class="admin-feature-box">
      <h3>Order Analysis</h3>
      <a href="#" class="admin-feature-btn">Open Panel</a>
    </div> -->
    <!-- <div class="admin-feature-box">
      <h3>Category Management</h3>
      <a href="#" class="admin-feature-btn">Open Panel</a>
    </div> -->
  </div>
</section>
</div>


<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>



</body>
</html>