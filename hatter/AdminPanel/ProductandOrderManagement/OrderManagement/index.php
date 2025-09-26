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
   .empty-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#f6f8fb;
}

    .empty-state{
        max-width:400px;
        margin:4rem auto;
        padding:2rem;
        text-align:center;
        background:#fff;
        border-radius:12px;
        box-shadow:0 6px 18px rgba(0,0,0,.08);
    }
    .empty-state i{
        font-size:3rem;
        color:#999;
        margin-bottom:1rem;
    }
    .empty-state h2{
        margin:0 0 .5rem;
        font-size:1.4rem;
    }
    .empty-state p{
        color:#666;
        margin:0 0 1rem;
    }
    .empty-state .btn{
        padding:.6rem 1.2rem;
        background:#2e8b57;
        color:#fff;
        border-radius:8px;
        text-decoration:none;
        display:inline-block;
    }
    .empty-state .btn:hover{ background:#256d46; }


    </style>
</head>
<body>

    
<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>
<div class="bg">
<main class="admin-panel-layout">
  <aside class="admin-sidebar">
  <h2 class="admin-menu-btn-h3"> 
        Order Management
  </h2>
    <!-- Filtreleme Butonları -->
    <button class="admin-menu-btn" onclick="filterOrders('active')">Active Orders</button>
    <button class="admin-menu-btn" onclick="filterOrders('shipped')">Orders Placed for Shipping</button>
    <button class="admin-menu-btn" onclick="filterOrders('preparing')">Orders in Preparation</button>
  </aside>

  <section class="admin-dynamic-area">
    <div id="orders" class="panel-section">
        <img src="/hatter/images/yh.jpg">
    </div>
        <!-- Sipariş Listeleme Bölümü -->
        <div id="order-list" class="panel-section" style="display:none;">
        <h2 id="order-list-title">Active Orders</h2> <!-- Başlık başlangıçta Aktif Siparişler olarak ayarlandı -->
        <div id="order-table-container">Loading...</div> <!-- Siparişler yüklenecek alan -->
    </div>

    <!-- Aktif Siparişler Section -->
    <div id="active-orders" class="panel-section" style="display:none;">
        <h2>Active Orders</h2>
        <div id="active-orders-container">Active Orders Loading...</div>
    </div>

    <!-- Kargoya Verilen Siparişler Section -->
    <div id="shipped-orders" class="panel-section" style="display:none;">
        <h2>Orders Placed for Shipping</h2>
        <div id="shipped-orders-container">Orders Placed for Shipping Loading...</div>
    </div>

    <!-- Hazırlanmakta Olan Siparişler Section -->
    <div id="preparing-orders" class="panel-section" style="display:none;">
        <h2>Hazırlanmakta Olan Siparişler</h2>
        <div id="preparing-orders-container">Orders in Preparation Loading...</div>
    </div>
  </section>
</main>
</div>

<script>
function filterOrders(status) {
    // Başlık kısmını güncelle
    let title = '';
    let activeSection = '';
    
    // İlgili section'ı göster
    if (status === 'active') {
        title = 'Aktif Siparişler';
        activeSection = 'active-orders';
    } else if (status === 'shipped') {
        title = 'Kargoya Verilen Siparişler';
        activeSection = 'shipped-orders';
    } else if (status === 'preparing') {
        title = 'Hazırlanmakta Olan Siparişler';
        activeSection = 'preparing-orders';
    }

    // Başlığı güncelle
    document.getElementById('order-list-title').innerText = title;

    // Tüm sectionları gizle
    document.querySelectorAll('.panel-section').forEach(sec => {
        sec.style.display = 'none';
    });

    // İlgili section'ı göster
    document.getElementById(activeSection).style.display = 'block';

    // Veritabanından filtrelenmiş siparişleri yükleyelim
    let url = `/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/filter-orders.php?status=${status}`;
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            if (status === 'active') {
                document.getElementById('active-orders-container').innerHTML = data;
            } else if (status === 'shipped') {
                document.getElementById('shipped-orders-container').innerHTML = data;
            } else if (status === 'preparing') {
                document.getElementById('preparing-orders-container').innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (status === 'active') {
                document.getElementById('active-orders-container').innerHTML = 'Active orders could not be loaded.';
            } else if (status === 'shipped') {
                document.getElementById('shipped-orders-container').innerHTML = 'Orders placed for shipping could not be loaded.';
            } else if (status === 'preparing') {
                document.getElementById('preparing-orders-container').innerHTML = ' Orders in progress could not be loaded.';
                  
            }
        });
}

function updateOrderStatus(orderId, newStatus){
  const url = '/hatter/AdminPanel/ProductandOrderManagement/OrderManagement/update-order-status.php';
  fetch(url, {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id=' + encodeURIComponent(orderId) + '&status=' + encodeURIComponent(newStatus)
  })
  .then(r => r.json())
  .then(j => {
    if (j.ok) {
      // aktif sekmeyi yeniden yükle
      // hangi sekmedeysek onu korumak için status’u tekrar çağır
      filterOrders(newStatus === 'cancelled' ? 'active' : newStatus);
    } else {
      alert(j.error || 'Güncellenemedi');
    }
  })
  .catch(() => alert('Ağ hatası'));
}

/* Sayfa açılınca varsayılan sekmeyi getir */
document.addEventListener('DOMContentLoaded', () => filterOrders('active'));
</script>



<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>

