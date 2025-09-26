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
        form label {
          font-weight:bold;
      }
    </style>
</head>
<body>
<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>

<div class="bg">
    <main class="admin-panel-layout">
        <aside class="admin-sidebar">
            <h2 class="admin-menu-btn-h3">Contact Messages</h2>
            <button class="admin-menu-btn" onclick="filterOrders('listMessages')">List Messages</button>
        </aside>

        <section class="admin-dynamic-area">
            <div id="orders" class="panel-section">
                <img src="/hatter/images/yh.jpg" alt="Background Image">
            </div>

            <div id="list-Messages" class="panel-section" style="display:none;">
                <h2 id="contactMessage-list-title">List Messages</h2>
                <div id="messages-container">Messages loading...</div>
            </div>
        </section>
    </main>
</div>
<div id="overlay"></div>
<div id="postContentPopup" class="content-popup">
  <button class="close-btn">✖</button>
  <h3>Message Content</h3>
  <p id="postContentText"></p>
</div>
<script>
    // JavaScript fonksiyonu, kullanıcıları listeleme için
    function filterOrders(status) {
  let title = '';
  let activeSection = '';

  if (status === 'listMessages') {
    title = 'Contact Messages List';
    activeSection = 'list-Messages';
  }

  const titleEl = document.getElementById('contactMessage-list-title');
  if (titleEl) titleEl.innerText = title;

  document.querySelectorAll('.panel-section').forEach(sec => sec.style.display = 'none');

  const target = document.getElementById(activeSection);
  if (!target) return;
  target.style.display = 'block';

  const url = `/hatter/AdminPanel/ContactandPageSettings/ContactMessages/filter-messages.php?status=${encodeURIComponent(status)}`;

  fetch(url)
    .then(r => r.text())
    .then(html => {
      if (status === 'listMessages') {
        const box = document.getElementById('messages-container');
        if (box) box.innerHTML = html;
      }
    })
    .catch(err => {
      console.error('Error:', err);
      const box = document.getElementById('messages-container');
      if (box) box.innerHTML = 'Mesajlar yüklenemedi';
    });
}


</script>

<script>

function deletePost(id) {
    if (confirm("Bu mesajı silmek istediğinize emin misiniz?")) {
        fetch(`/hatter/AdminPanel/ContactandPageSettings/ContactMessages/deleteMessage.php?id=${id}`)
            .then(response => response.text())
            .then(data => {
                alert(data);
                filterOrders('listMessages');
            })
            .catch(error => {
                alert("Silme işlemi başarısız: " + error);
            });
    }
}

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
