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
            <button class="admin-menu-btn" onclick="filterOrders('listPosts')">List Users Posts</button>
        </aside>

        <section class="admin-dynamic-area">
            <div id="orders" class="panel-section">
                <img src="/hatter/images/yh.jpg" alt="Background Image">
            </div>

            <div id="list-Posts" class="panel-section" style="display:none;">
                <h2 id="userPost-list-title">List Users Posts</h2>
                <div id="Posts-container">List Users Posts loading...</div>
            </div>
        </section>
    </main>
</div>
<div id="overlay"></div>
<div id="postContentPopup" class="content-popup">
  <button class="close-btn">✖</button>
  <h3>Post Content</h3>
  <p id="postContentText"></p>
</div>



<script>
    
    function filterOrders(status) {
        let title = '';
        let activeSection = '';

        if (status === 'listPosts') {
            title = 'User Posts';
            activeSection = 'list-Posts';
        }

        // Başlık kısmını güncelle
        document.getElementById('userPost-list-title').innerText = title;

        // Diğer sectionları gizle
        document.querySelectorAll('.panel-section').forEach(sec => {
            sec.style.display = 'none';
        });

        // İlgili section'ı göster
        document.getElementById(activeSection).style.display = 'block';

        // Veritabanından kullanıcı verisini yükleyelim
        let url = `/hatter/AdminPanel/UserContentControl/blogContentManagement/posts.php?status=${status}`;

        fetch(url)
            .then(response => response.text())
            .then(data => {
                // Veriyi yükle
                if (status === 'listPosts') {
                    document.getElementById('Posts-container').innerHTML = data;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('Posts-container').innerHTML = 'Posts Download Fail';
            });
    }
</script>

<script>

function deletePost(postId) {
    if (confirm('Bu postu silmek istediğinizden emin misiniz?')) {
        fetch(`/hatter/AdminPanel/UserContentControl/blogContentManagement/deletePost.php?id=${postId}`, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            filterOrders('listPosts'); // Yeniden yükle
        })
        .catch(error => {
            alert('Hata oluştu: ' + error);
        });
    }
}

</script>

<script>
document.body.addEventListener("click", function (e) {
  if (e.target.classList.contains("read-btn")) {
    const content = e.target.getAttribute("data-content");
    document.getElementById("postContentText").innerText = content;
    document.getElementById("postContentPopup").style.display = "block";
  }

  if (e.target.classList.contains("close-btn")) {
    document.getElementById("postContentPopup").style.display = "none";
  }
});


document.body.addEventListener("click", function (e) {
  if (e.target.classList.contains("read-btn")) {
    const content = e.target.getAttribute("data-content");
    document.getElementById("postContentText").innerText = content;
    document.getElementById("postContentPopup").style.display = "block";
    document.getElementById("overlay").style.display = "block";
  }

  if (e.target.classList.contains("close-btn") || e.target.id === "overlay") {
    document.getElementById("postContentPopup").style.display = "none";
    document.getElementById("overlay").style.display = "none";
  }
});

</script>





<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>
</body>
</html>
