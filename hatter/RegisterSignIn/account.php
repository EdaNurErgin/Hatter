<?php session_start(); ?>
<?php
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

$userId = $_SESSION["user_id"] ?? null;

if ($userId) {
  $stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  $adSoyad = $user["full_name"] ?? "";
  $email = $user["email"] ?? "";
  $phone = $user["phone"] ?? "";
} else {
  $adSoyad = $email = $phone = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hatter</title>
  <link rel="stylesheet" href="/hatter/styles/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    #account { display: flex; min-height: 100vh; background-color: #f6f6f6; }
    .sidebar { width: 250px; background-color: #fff; border-right: 1px solid #ddd; padding: 40px; }
    .sidebar h3 { margin-bottom: 15px; font-size: 16px; color: #666; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar li { margin-bottom: 15px; }
    .sidebar a { text-decoration: none; color: #333; font-size: 15px; display: flex; align-items: center; gap: 10px; padding: 8px; border-radius: 6px; }
    .sidebar a:hover, .sidebar a.active { background-color: #8ed49d; font-weight: bold; color: var(--main-color); }
    .content { flex-grow: 1; padding: 40px; background-color: white; }
    .form-sections { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; background: #fff; }
    .form-box { padding: 20px; border: 1px solid #ddd; border-radius: 6px; }
    .form-group { margin-bottom: 15px; }
    input, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    .btn { background-color: var(--main-color); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    .btn:disabled { background-color: #ccc; cursor: not-allowed; }
  </style>
</head>

<body>

<?php include 'C:/xampp/htdocs/hatter/root/header.php'; ?>

<div id="account">
  <aside class="sidebar">
    <h3>Account & Help</h3>
    <ul>
      <li><a href="#" id="btn-kullanici" class="active"><i class="fa-solid fa-user"></i> User Information </a></li>
      <li><a href="#" id="btn-adres"><i class="fa-solid fa-location-dot"></i> Address Information</a></li>
      <li><a href="#" id="btn-siparisler"><i class="fa-solid fa-box"></i> All Orders</a></li>
      <li><a href="/hatter/RegisterSignIn/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
    </ul>
  </aside>

  <main class="content">
    <div class="form-sections" id="user-sections">
    <section class="form-box">
      <h3>User Information</h3>
      <form method="POST" action="/hatter/RegisterSignIn/UserInformation/userUpdate.php" style="padding:20px">
        <div class="form-group"><label>Name Surname</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($adSoyad) ?>" required>
        </div>
        <div class="form-group"><label>E-Mail</label>
          <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="form-group"><label>Phone</label>
          <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn">Update</button>
        </div>
      </form>
    </section>


    <section class="form-box">
      <h3>Password Update</h3>
      <form method="POST" action="/hatter/RegisterSignIn/UserInformation/passwordUpdate.php" style="padding:20px">
        <div class="form-group">
          <label>Now Password</label>
          <input type="password" name="current_password" placeholder="Eski Şifre" required>
        </div>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="new_password" placeholder="Yeni Şifre" required>
          <small>
            Your password must be at least 10 characters long and include 
            <b>1 uppercase letter</b>, <b>1 lowercase letter</b>, and a <b>number</b>.
          </small>

        </div>
        <div class="form-group">
          <label>New Password Again</label>
          <input type="password" name="new_password_repeat" placeholder="Yeni Şifre tekrar" required>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn">Update</button>
        </div>
      </form>
    </section>
    </div>

    <div id="adresler" class="form-box" style="display: none;">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="padding-under:20px">Address Informations</h3>
        <button class="btn" id="yeniAdresBtn">
          <i class="fa-solid fa-plus"></i> New Address Information Add
        </button>
      </div>

      <form id="adresForm" method="POST" action="/hatter/RegisterSignIn/addressAdd.php" style="margin-top: 20px; display: none;">
        <div class="form-group">
          <label>Address Content</label>
          <textarea name="content" rows="4" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
        </div>
        <button type="submit" class="btn">Save</button>
      </form>


      <div id="adres-listesi" style="margin-top: 20px;">Yükleniyor...</div>
    </div>

    <div id="siparisler" class="form-box" style="display: none;">
      <h3 style="padding-under:20px">All Orders</h3>
      <p>Loading...</p>
    </div>
  </main>
</div>

<script>
window.addEventListener("DOMContentLoaded", function () {
  fetch("/hatter/RegisterSignIn/addressList.php")
    .then(res => res.text())
    .then(data => {
      document.getElementById("adres-listesi").innerHTML = data;
    })
    .catch(err => {
      document.getElementById("adres-listesi").innerHTML = "Adresler yüklenemedi.";
      console.error(err);
    });
});

const btnKullanici = document.getElementById("btn-kullanici");
const btnAdres = document.getElementById("btn-adres");
const btnSiparisler = document.getElementById("btn-siparisler");
const btnYardim = document.getElementById("btn-yardim");
const userSections = document.getElementById("user-sections");
const adresler = document.getElementById("adresler");
const siparisler = document.getElementById("siparisler");

const bolumler = [userSections, adresler, siparisler];
const butonlar = [btnKullanici, btnAdres, btnSiparisler];

function aktifBolumGoster(aktifDiv, aktifBtn) {
  bolumler.forEach(div => div.style.display = "none");
  butonlar.forEach(btn => btn.classList.remove("active"));
  aktifDiv.style.display = aktifDiv === userSections ? "grid" : "block";
  aktifBtn.classList.add("active");
}

btnKullanici.addEventListener("click", function(e) {
  e.preventDefault();
  aktifBolumGoster(userSections, btnKullanici);
});

btnAdres.addEventListener("click", function(e) {
  e.preventDefault();
  aktifBolumGoster(adresler, btnAdres);
  fetch("/hatter/RegisterSignIn/addressList.php")
    .then(res => res.text())
    .then(data => {
      document.getElementById("adres-listesi").innerHTML = data;
    });
});

btnSiparisler.addEventListener("click", function(e) {
  e.preventDefault();
  aktifBolumGoster(siparisler, btnSiparisler);
  fetch("/hatter/RegisterSignIn/orders/orderList.php")
    .then(res => res.text())
    .then(data => {
      siparisler.innerHTML = "<h3>Tüm Siparişlerim</h3>" + data;
    })
    .catch(err => {
      siparisler.innerHTML = "<p>Siparişler yüklenemedi.</p>";
      console.error(err);
    });
});


btnYardim.addEventListener("click", function(e) {
  e.preventDefault();
  aktifBolumGoster(yardim, btnYardim);
  // AJAX ile help içeriğini al
  fetch("/hatter/RegisterSignIn/help/helpList.php")
    .then(res => res.text())
    .then(data => {
      document.getElementById("faq-accordion").innerHTML = data;
      bindFAQEvents(); // gelen içeriğe event bağla
    })
    .catch(err => {
      document.getElementById("faq-accordion").innerHTML = "<p>Yardım içeriği yüklenemedi.</p>";
      console.error(err);
    });


  
});


function bindFAQEvents() {
  document.querySelectorAll(".faq-item").forEach(item => {
    const question = item.querySelector(".faq-question");
    const answer = item.querySelector(".faq-answer");
    const icon = item.querySelector(".faq-toggle-icon");

    question.addEventListener("click", () => {
      const isOpen = answer.style.display === "block";

      // Tüm soruları kapat
      document.querySelectorAll(".faq-answer").forEach(a => a.style.display = "none");
      document.querySelectorAll(".faq-question").forEach(q => q.classList.remove("active"));
      document.querySelectorAll(".faq-toggle-icon").forEach(i => i.textContent = "+");

      // Eğer bu açık değilse aç
      if (!isOpen) {
        answer.style.display = "block";
        question.classList.add("active");
        icon.textContent = "–";
      }
    });
  });
}


document.getElementById("yeniAdresBtn").addEventListener("click", function () {
  const form = document.getElementById("adresForm");
  form.reset();
  form.style.display = form.style.display === "none" ? "block" : "none";
  form.action = "/hatter/RegisterSignIn/addressAdd.php";
  const oldId = form.querySelector("input[name='id']");
  if (oldId) oldId.remove();
  form.querySelector('button[type="submit"]').innerText = "Kaydet";
});

document.getElementById("adresForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);

  fetch(this.action, {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(cevap => {
    if (cevap === "eklendi" || cevap === "guncellendi") {
      this.reset();
      this.style.display = "none";
      this.action = "/hatter/RegisterSignIn/addressAdd.php";

      const hiddenInput = this.querySelector("input[name='id']");
      if (hiddenInput) hiddenInput.remove();

      this.querySelector('button[type="submit"]').innerText = "Kaydet";

      fetch("/hatter/RegisterSignIn/addressList.php")
        .then(res => res.text())
        .then(data => {
          document.getElementById("adres-listesi").innerHTML = data;
        });

      // 👇 Snackbar mesajını burada göster!
      if (cevap === "eklendi") showSnackbar("Adres başarıyla eklendi.");
      if (cevap === "guncellendi") showSnackbar("Adres başarıyla güncellendi.");

    } else {
      alert("Hata: " + cevap);
    }
  });
});


function adresSil(form) {
  if (!confirm("Bu adresi silmek istediğine emin misin?")) return false;
  const formData = new FormData(form);
  fetch("/hatter/RegisterSignIn/addressDelete.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(cevap => {
    if (cevap === "silindi") {
      fetch("/hatter/RegisterSignIn/addressList.php")
        .then(res => res.text())
        .then(data => {
          document.getElementById("adres-listesi").innerHTML = data;
        });
      showSnackbar("Adres başarıyla silindi.");


    } else {
      alert("Silinemedi: " + cevap);
    }
  });
  return false;
}

// 👇 Güncelleme butonu için
function adresGuncelleFormunuAc(content, id) {
  const form = document.getElementById("adresForm");
  form.style.display = "block";
  form.content.value = content;
  form.action = "/hatter/RegisterSignIn/addressUpdate.php";

  const oldId = form.querySelector("input[name='id']");
  if (oldId) oldId.remove();

  const hiddenId = document.createElement("input");
  hiddenId.type = "hidden";
  hiddenId.name = "id";
  hiddenId.value = id;
  form.appendChild(hiddenId);

  form.querySelector('button[type="submit"]').innerText = "Güncelle";
}
</script>

<div id="snackbar"></div>

<script>
function showSnackbar(message) {
  const snackbar = document.getElementById("snackbar");
  snackbar.textContent = message;
  snackbar.classList.add("show");
  setTimeout(() => {
    snackbar.classList.remove("show");
  }, 3000);
}
</script>

<?php if (isset($_SESSION["message"])): ?>
  <script>
    window.addEventListener("DOMContentLoaded", function () {
      showSnackbar("<?= htmlspecialchars($_SESSION['message'], ENT_QUOTES) ?>");
    });
  </script>
  <?php unset($_SESSION["message"]); ?>
<?php endif; ?>



<?php include 'C:/xampp/htdocs/hatter/root/footer.php'; ?>
</body>
</html>