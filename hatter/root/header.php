<!-- <?php
session_start();




?> -->


<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

if (!function_exists('asset_url')) {
  function asset_url(string $path): string {
    $p = str_replace('\\','/', trim($path));
    if ($p !== '' && $p[0] !== '/') { $p = '/hatter/' . ltrim($p, '/'); }
    return htmlspecialchars($p, ENT_QUOTES, 'UTF-8');
  }
}

$cart = (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? $_SESSION['cart'] : [];
$here = $_SERVER['REQUEST_URI'] ?? '/hatter/index.php';

$cartTotal = 0.0;
foreach ($cart as $it) {
  $cartTotal += ((float)$it['price'] * (int)$it['qty']);
}
?>

<!-- header section start -->
<header class="header">
    <a href="/hatter/index.php" class="logo">
        <img src="/hatter/images/logo.jpg" alt="logo">
    </a>
    <nav class="navbar">
        <a href="/hatter/index.php" class="active">Home</a>
        <a href="/hatter/Products/index.php">Products</a>
        <!-- <a href="/hatter/NewProducts/index.php">New Products</a>
        <a href="/hatter/Deals/index.php">Deals</a> -->
        <a href="/hatter/AboutUs/index.php">About Us</a>
        
        <a href="/hatter/Blog/index.php">Blogs</a>
    </nav>

    <div class="buttons">
        <button id="search-btn">
            <i class="fa-brands fa-searchengin"></i>
        </button>
        <button id="cart-btn">
            <i class="fa-solid fa-cart-shopping"></i>
        </button>
        <button id="menu-btn">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Giriş kontrolü -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Giriş yapan kullanıcı -->
          

            <button id="login-btn">
               <i class="fa-solid fa-house-user" id="signafter"> </i>
            </button>
            <script>
                document.getElementById("login-btn").addEventListener("click", function () {
                    window.location.href = "http://localhost/hatter/RegisterSignIn/account.php";
                });
            </script>
        <?php else: ?>
            
            <!-- Giriş yapmamış kullanıcı -->
            <button id="login-btn">
                <i class="fa-solid fa-user"></i>
            </button>
            <script>
                document.getElementById("login-btn").addEventListener("click", function () {
                    window.location.href = "http://localhost/hatter/RegisterSignIn/index.php";
                });
            </script>
        <?php endif; ?>
    </div>

    <!-- Search bar -->
    <div class="search-form">
        <input type="text" class="search-input" id="search-box" placeholder="search here" />
        <i class="fas fa-search"></i>
    </div>

    <!-- Sepet
    <div class="cart-items-container ">
            <div class="cart-item">
                <i class="fas fa-times"></i>
                <img src="images/menu/spk2.jpg" alt="menu">
                <div class="content">
                    <h3>cart item 01</h3>
                    <div class="price">$15.99/-</div>
                </div>
            </div>
            <div class="cart-item">
                <i class="fas fa-times"></i>
                <img src="images/menu/spk3.jpg" alt="menu">
                <div class="content">
                    <h3>cart item 01</h3>
                    <div class="price">$15.99/-</div>
                </div>
            </div>
            <div class="cart-item">
                <i class="fas fa-times"></i>
                <img src="images/menu/spk4.jpg" alt="menu">
                <div class="content">
                    <h3>cart item 01</h3>
                    <div class="price">$15.99/-</div>
                </div>
            </div>
            <div class="cart-item">
                <i class="fas fa-times"></i>
                <img src="images/menu/spk1.jpg" alt="menu">
                <div class="content">
                    <h3>cart item 01</h3>
                    <div class="price">$15.99/-</div>
                </div>
            </div>
            <a href="#" class="btn"> check outnow</a>
   </div> -->

  <!-- Cart -->
<div class="cart-items-container"
     role="dialog" aria-modal="true" aria-labelledby="cart-title">

  <?php if (empty($cart)): ?>
    <div class="cart-item" role="status" aria-live="polite">
      <div class="content">
        <h3 id="cart-title">Cart is empty</h3>
        <div class="price">Click the + button to add a product</div>
      </div>
    </div>

  <?php else: ?>
    <!-- Başlık (isteğe bağlı) -->
    <!-- <div class="cart-header" style="padding:12px 16px;border-bottom:1px solid #f0f0f0;">
      <h3 id="cart-title" style="font-size:1.8rem;margin:0;">Sepet</h3>
    </div> -->

    <!-- Sadece ürün listesi scroll -->
    <div class="cart-scroll" role="list">
      <?php foreach ($cart as $it): ?>
        <div class="cart-item" role="listitem">
          <a class="remove"
             href="/hatter/cart/remove.php?id=<?= (int)$it['id'] ?>&r=<?= urlencode($here) ?>"
             title="Kaldır" aria-label="Ürünü sepetten kaldır">
            <i class="fas fa-times" aria-hidden="true"></i>
          </a>

          <img src="<?= asset_url($it['image']) ?>"
               alt="<?= htmlspecialchars($it['name']) ?> görseli">

          <div class="content">
            <h3 style="margin:0;"><?= htmlspecialchars($it['name']) ?></h3>
            <div class="price">
              $<?= number_format((float)$it['price'], 2) ?>
              × <?= (int)$it['qty'] ?>
              = <strong>$<?= number_format((float)$it['price'] * (int)$it['qty'], 2) ?></strong>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Altta sabit özet / checkout -->
    <div class="cart-summary">
      <div class="summary-row">
        <span>Toplam</span>
        <strong>$<?= number_format($cartTotal, 2) ?></strong>
      </div>
      <a href="/hatter/checkout/index.php" class="btn w-full" aria-label="Satın alma adımına geç">
        check out now
      </a>
    </div>
  <?php endif; ?>
</div>


</header>
<!-- header section end -->

<script>
// AJAX add-to-cart (event delegation) - link ve button destekli
document.addEventListener('click', async function (e) {
  const el = e.target.closest('a.add-to-cart,[data-add-to-cart]');
  if (!el) return;

  e.preventDefault();
  const url = el.matches('a.add-to-cart') ? el.href : el.getAttribute('data-add-to-cart');

  try {
    const res  = await fetch(url, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    });
    const type = res.headers.get('content-type') || '';
    if (!type.includes('application/json')) { window.location.href = url; return; }

    const data = await res.json();
    if (!data || !data.ok) { window.location.href = url; return; }

    const cartPanel = document.querySelector('.cart-items-container');
    if (cartPanel) {
      cartPanel.classList.add('active');
      const scroll  = cartPanel.querySelector('.cart-scroll');
      const summary = cartPanel.querySelector('.cart-summary');
      if (scroll)  scroll.innerHTML  = data.items_html || '';
      if (summary) summary.innerHTML = data.summary_html || '';
    }
  } catch (err) {
    window.location.href = url;
  }
});

</script>
