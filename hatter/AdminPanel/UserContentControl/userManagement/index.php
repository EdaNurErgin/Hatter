<?php
session_start();

// Admin kontrolü
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
        .bg {
            min-height: 100vh;
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
    <main class="admin-panel-layout">
        <aside class="admin-sidebar">
            <h2 class="admin-menu-btn-h3">Users Management</h2>
            <button class="admin-menu-btn" onclick="filterOrders('listUsers')">List Users</button>
        </aside>

        <section class="admin-dynamic-area">
            <div id="orders" class="panel-section">
                <img src="/hatter/images/yh.jpg" alt="Background Image">
            </div>

            <div id="list-Users" class="panel-section" style="display:none;">
                <h2 id="user-list-title">List Users</h2>
                <div id="users-container">Users loading...</div>
            </div>
        </section>
    </main>
</div>

<script>
    // JavaScript fonksiyonu, kullanıcıları listeleme için
    function filterOrders(status) {
        let title = '';
        let activeSection = '';

        if (status === 'listUsers') {
            title = 'User List';
            activeSection = 'list-Users';
        }

        // Başlık kısmını güncelle
        document.getElementById('user-list-title').innerText = title;

        // Diğer sectionları gizle
        document.querySelectorAll('.panel-section').forEach(sec => {
            sec.style.display = 'none';
        });

        // İlgili section'ı göster
        document.getElementById(activeSection).style.display = 'block';

        // Veritabanından kullanıcı verisini yükleyelim
        let url = `/hatter/AdminPanel/UserContentControl/userManagement/filter-users.php?status=${status}`;

        fetch(url)
            .then(response => response.text())
            .then(data => {
                // Veriyi yükle
                if (status === 'listUsers') {
                    document.getElementById('users-container').innerHTML = data;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('users-container').innerHTML = 'Kullanıcılar yüklenemedi.';
            });
    }
</script>

<script>function deleteUser(userId) {
    if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
        // AJAX isteği gönder
        fetch(`/hatter/AdminPanel/UserContentControl/userManagement/deleteUser.php?id=${userId}`, {
            method: 'GET',
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Silme işlemi başarılıysa, başarı mesajını göster
             // Sayfayı yenileyerek güncel kullanıcı listesini göster
        })
        .catch(error => {
            console.error('Hata:', error);
            alert('Bir hata oluştu!');
        });
    }
}
</script>

<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>
</body>
</html>
