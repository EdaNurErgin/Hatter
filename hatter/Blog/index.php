<?php
session_start();
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hatter | Blog</title>
  <link rel="stylesheet" href="/hatter/styles/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php include 'C:/xampp/htdocs/hatter/root/header.php'; ?>

<section class="blogs">
  
  <div class="container">

    <!-- Kullanıcı giriş yaptıysa blog ekleme formu -->
    <?php if (isset($_SESSION["user_id"])): ?>
    <h1 class="heading"><span>My</span> Post</h1>
    <form action="add_blog_post.php" method="POST" enctype="multipart/form-data" class="blog-form">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="content" placeholder="Explain" required></textarea>
        <input type="file" name="image" accept="image/*">
        <button type="submit">Post Add</button>
    </form>
    <?php endif; ?>



    <!-- Blog başlığı -->
    <h1 class="heading">OUR <span>Posts</span></h1>

    <!-- Blog postları listeleniyor -->
    <div class="box-container">
      <?php
      $stmt = $conn->prepare("SELECT blog_posts.*, users.full_name FROM blog_posts INNER JOIN users ON blog_posts.user_id = users.id ORDER BY created_at DESC LIMIT 5");
      $stmt->execute();
      $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($posts as $post): ?>
        <div class="box">
          <div class="image">
           <?php
            $imagePath = (!empty($post['image'])) ? "/hatter/uploads/" . htmlspecialchars($post['image']) : "/hatter/images/hh.jpg";
           ?>
            <img src="<?php echo $imagePath; ?>" alt="blog">



          </div>
          <div class="content">
            <a class="title"><?php echo htmlspecialchars($post['title']); ?></a>
            <span>By <?php echo htmlspecialchars($post['full_name']); ?> / <?php echo date("d M Y", strtotime($post['created_at'])); ?></span>

            <p class="blog-text short"><?php echo htmlspecialchars($post['content']); ?></p>
            <button class="toggle-btn">Show More</button>
         </div>

        </div>
      <?php endforeach; ?>
    </div>

    
    <div class="load-more" style="text-align: center; margin-top: 20px;">
      <button class="btn">Load More</button>
    </div>

  </div>
</section>

<!-- devamini goster -->
<script>
document.querySelectorAll(".toggle-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const text = this.previousElementSibling;
    text.classList.toggle("short");
    this.textContent = text.classList.contains("short") ? "Show More" : "Show Less";
  });
});
</script>


<?php include 'C:/xampp/htdocs/hatter/root/footer.php'; ?>

</body>
</html>
