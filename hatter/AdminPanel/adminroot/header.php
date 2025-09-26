
<!-- header section start -->
<header class="header">
    <a href="/hatter/AdminPanel/index.php" class="logo">
        <img src="/hatter/images/logo.jpg" alt="logo">
    </a>
    <nav class="navbar">
        <a href="/hatter/AdminPanel/index.php" class="active">Home</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="/hatter/AdminPanel/ProductandOrderManagement/index.php">Product and Order Management</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="/hatter/AdminPanel/UserContentControl/index.php">User & Content Control</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="/hatter/AdminPanel/ContactandPageSettings/index.php">Contact and Page Settings</a>
        
    </nav>
    
    <div class="buttons">
        <button id="search-btn">
            <i class="fa-brands fa-searchengin"></i>
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
                    window.location.href = "http://localhost/hatter/AdminPanel/adminAccount.php";
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

    <!-- Sepet -->
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
        </div>
</header>
<!-- header section end -->

