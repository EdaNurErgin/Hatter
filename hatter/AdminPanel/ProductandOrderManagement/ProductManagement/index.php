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
        /* background: url(/hatter/images/n.jpg) no-repeat; */
        background-size: cover;
        background-position: center;
        margin-top: -14.5rem;
        display: grid;
        align-items: center; 
   }
   .pagination-bar .page-btn {
        padding:.45rem .7rem; border:1px solid #e5e7eb; background:#fff;
        border-radius:8px; cursor:pointer;
    }
   .pagination-bar .page-btn[disabled]{ opacity:.5; cursor:not-allowed; }
    </style>
</head>
<body>

    
<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>
<div class="bg">
<main class="admin-panel-layout">
  <aside class="admin-sidebar">
    <h2 class="admin-menu-btn-h3"> 
    Product Order Management</h2>
    <button class="admin-menu-btn" onclick="showSection('add-product')">Product Add</button>
    
    
    <button class="admin-menu-btn" onclick="showSection('add-category')">Category Add</button>
    
    <button class="admin-menu-btn" onclick="showSection('product-list')">Products List</button>
    <button class="admin-menu-btn" onclick="showSection('category-list')">Category List</button>
  </aside>

  <section class="admin-dynamic-area">
    <div id="orders" class="panel-section">
        <img src="/hatter/images/yh.jpg">
    </div>
    <div id="add-product" class="panel-section" style="display:none;">
    <h2>New Product Add</h2>
    <form id="addProductForm" action="/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/add-product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" id="productId" name="productId">
        <input type="hidden" id="oldProductImage" name="oldProductImage">

        <label for="productName">Product Name</label>
        <input type="text" id="productName" name="productName" required><br>

        <label for="productPrice">Price:</label>
        <input type="number" id="productPrice" name="productPrice" step="0.01" required><br>

        <label for="productCategory">Category:</label>
        <select id="productCategory" name="productCategory" required>
            <!-- Kategorileri burada listeliyoruz -->
            <?php
                require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";
                $sql = "SELECT * FROM categories";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($categories as $category) {
                    echo '<option value="' . htmlspecialchars($category["id"]) . '">' . htmlspecialchars($category["name"]) . '</option>';
                }
            ?>
        </select><br>

        <label for="productStock">Stock:</label>
        <input type="number" id="productStock" name="productStock" required><br>

        <label for="productDescription">Açıklama:</label>
        <textarea id="productDescription" name="productDescription" required></textarea><br>
        <img id="productPreview" src="" alt="Ürün Görseli" style="width: 100px; height: 100px; object-fit: cover; display: none; margin-bottom: 1rem;">

        <label for="productImage">Product Image:</label>
        <input type="file" id="productImage" name="productImage" accept="image/*" required><br>

        <button type="submit">Product Add</button>
    </form>
   </div>



    <div id="add-category" class="panel-section" style="display:none;">
        <h2>New Category Add</h2>
        <form id="addCategoryForm" action="/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/add-category.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="categoryId" name="categoryId">
            <input type="hidden" id="oldCategoryImage" name="oldCategoryImage">

            <label for="categoryName">Category Name:</label>
            <input type="text" id="categoryName" name="categoryName" required><br>

            <label for="categorySlug">Slug (URL):</label>
            <input type="text" id="categorySlug" name="categorySlug"><br>

            <label for="categoryImage">Category Image:</label>
            <input type="file" id="categoryImage" name="categoryImage" accept="image/*"><br>
            <!-- Resim önizlemesi -->
            <img id="categoryPreview" src="" alt="Kategori Görseli" style="width: 100px; height: 100px; object-fit: cover; display: none;">

            <button type="submit">Category Add</button> <!-- Buton başlangıçta "Kategori Ekle" -->
        </form>
    </div>



    <div id="product-list" class="panel-section" style="display:none;">
    <h2>Products Lis</h2>
    <div id="product-table-container">Loading...</div>

    </div>
    <div id="category-list" class="panel-section" style="display:none;">Category List
    <h2>Category List</h2>   
    </div>
  </section>
</main>



</div>


<script>
function showSection(id) {
  document.querySelectorAll('.panel-section').forEach(sec => sec.style.display = 'none');
  document.getElementById(id).style.display = 'block';
}
</script>

<script>

function showSection(id) {
  document.querySelectorAll('.panel-section').forEach(sec => sec.style.display = 'none');
  document.getElementById(id).style.display = 'block';

  if (id === 'add-product')   resetProductForm();
  if (id === 'add-category')  resetCategoryForm();

  if (id === 'product-list')  loadProductPage(1);
  if (id === 'category-list') loadCategoryPage(1); // 🔸 değişiklik
}

// Global: Sayfalı KATEGORİ yükleme
function loadCategoryPage(page = 1) {
  const perPage = 10;
  const url = `/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/list-categories.php?page=${page}&per_page=${perPage}`;
  const container = document.getElementById('category-list');
  container.innerHTML = '<h2>Category List</h2><div>Loading...</div>';
  fetch(url)
    .then(r => r.text().then(t => ({ok:r.ok, status:r.status, text:t})))
    .then(({ok, status, text}) => {
      if (!ok) console.error('HTTP', status, text);
      container.innerHTML = text;
    })
    .catch(e => {
      container.innerHTML = '<h2>Category List</h2><div>Kategoriler yüklenemedi.</div>';
      console.error(e);
    });
}



// Global: Sayfalı ürün yükleme
function loadProductPage(page = 1) {
  const perPage = 10; // istersen değiştir
  const url = `/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/list-products.php?page=${page}&per_page=${perPage}`;
  const container = document.getElementById('product-table-container');
  container.innerHTML = 'Loading...';
  fetch(url)
    .then(r => r.text())
    .then(html => { container.innerHTML = html; })
    .catch(e => { container.innerHTML = 'Ürünler yüklenemedi.'; console.error(e); });
}

</script>


<script>
function editProduct(productId) {
    // Önce formu göster
    showSection('add-product');
    
    // Verileri backendden çekelim
    fetch('/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/get-product.php?id=' + productId)
    .then(response => response.json())
    .then(product => {
        // Formu dolduralım
        document.getElementById('productId').value = product.id;
        document.getElementById('productName').value = product.name;
        document.getElementById('productPrice').value = product.price;
        document.getElementById('productCategory').value = product.category_id;
        document.getElementById('productStock').value = product.stock;
        document.getElementById('productDescription').value = product.description;
        
        // Fotoğraf inputu değiştir
        document.getElementById('oldProductImage').value = product.image;
        // Buraya resim önizlemeyi gösterelim
        const preview = document.getElementById('productPreview');
        preview.src = '/hatter/' + product.image;  // Örn: uploads/kl.jpg
        preview.style.display = 'block';

        // Submit butonunun adını değiştir
        const submitButton = document.querySelector('#addProductForm button[type="submit"]');
        submitButton.textContent = "Ürünü Güncelle";

        // Formun action'ını değiştirelim
        document.getElementById('addProductForm').action = '/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/update-product.php';
    })
    .catch(error => {
        console.error('Ürün bilgileri alınamadı:', error);
    });
}
</script>


<script>
function deleteProduct(productId) {
    if (confirm("Bu ürünü silmek istediğinize emin misiniz?")) {
        fetch('/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/delete-product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(productId)
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            // Silmeden sonra ürün listesini yenile
            showSection('product-list');
        })
        .catch(error => {
            console.error('Silme hatası:', error);
        });
    }
}
</script>

<script>
function resetProductForm() {
    document.getElementById('addProductForm').reset();
    document.getElementById('productId').value = "";
    document.getElementById('oldProductImage').value = "";
    document.getElementById('productPreview').style.display = 'none';

    // Submit butonunu tekrar "Ürün Ekle" yap
    const submitButton = document.querySelector('#addProductForm button[type="submit"]');
    submitButton.textContent = "Ürün Ekle";

    // Form action'ı tekrar ürün eklemeye ayarla
    document.getElementById('addProductForm').action = '/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/add-product.php';
}

function resetCategoryForm() {
    document.getElementById('addCategoryForm').reset();
    document.getElementById('categoryId').value = "";

    const submitButton = document.querySelector('#addCategoryForm button[type="submit"]');
    submitButton.textContent = "Kategori Ekle";

    document.getElementById('addCategoryForm').action = '/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/add-category.php';
}



</script>

<script>
function editCategory(categoryId) {
    // Kategori düzenleme formunu göster
    showSection('add-category');
    
    // Verileri backend'den çekelim
    fetch('/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/get-category.php?id=' + categoryId)
    .then(response => response.json())
    .then(category => {
        // Formu dolduralım
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categorySlug').value = category.slug;
        document.getElementById('oldCategoryImage').value = category.image;

        // Kategori resmini güncelle (Önizleme)
        const preview = document.getElementById('categoryPreview');
        preview.src = '/hatter/' + category.image;  // Örn: uploads/kategori.jpg
        preview.style.display = 'block';  // Görseli göster

        // Submit butonunun adını değiştirelim
        const submitButton = document.querySelector('#addCategoryForm button[type="submit"]');
        submitButton.textContent = "Kategori Güncelle";  // Butonun metnini "Kategori Güncelle" olarak değiştir

        // Formun action'ını değiştirelim (güncelleme işlemi için)
        document.getElementById('addCategoryForm').action = '/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/update-category.php';
    })
    .catch(error => {
        console.error('Kategori bilgileri alınamadı:', error);
    });
}




// Kategori silme
function deleteCategory(categoryId) {
    if (confirm("Bu kategoriyi silmek istediğinize emin misiniz?")) {
        fetch('/hatter/AdminPanel/ProductandOrderManagement/ProductManagement/delete-category.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(categoryId)
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            showSection('category-list');  // Kategori listesini yeniden yükle
        })
        .catch(error => {
            console.error('Silme hatası:', error);
        });
    }
}



</script>



<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>

