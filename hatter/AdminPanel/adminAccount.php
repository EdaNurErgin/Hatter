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

<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/header.php'; ?>

<div id="account">
  <aside class="sidebar">
    <h3>Account & Help</h3>
    <ul>
      <li><a href="#" id="btn-kullanici" class="active"><i class="fa-solid fa-user"></i> User Information</a></li>
      <li><a href="#" id="btn-yardim"><i class="fa-solid fa-circle-question"></i> Help</a></li>
      <li><a href="/hatter/RegisterSignIn/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a></li>
    </ul>
  </aside>

  <main class="content">
    <div class="form-sections" id="user-sections">
      <section class="form-box">
        <h3>User Information</h3>
        <form method="POST" action="/hatter/RegisterSignIn/UserInformation/userUpdate.php" style="padding:10px;">
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
        <form method="POST" action="/hatter/RegisterSignIn/UserInformation/passwordUpdate.php" style="padding:10px;" >
          <div class="form-group">
            <label>Now Password</label>
            <input type="password" name="current_password" placeholder="Eski Şifre" required>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Yeni Şifre" required>
            <small>
              Your password must be at least 10 characters. <b>1 capital letter</b>, <b>
              1 lowercase letter</b> and <b>number</b> 
              should contain.
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

    <div id="yardim" class="form-box" style="display: none;">
      <h3>Help</h3>
      <div class="faq-container">
        <h3>Popular Questions</h3>
        <div id="faq-accordion">
          <p>Loading...</p>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
const btnKullanici = document.getElementById("btn-kullanici");
const btnYardim = document.getElementById("btn-yardim");
const userSections = document.getElementById("user-sections");
const yardim = document.getElementById("yardim");
const bolumler = [userSections, yardim];
const butonlar = [btnKullanici, btnYardim];

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

btnYardim.addEventListener("click", function(e) {
  e.preventDefault();
  aktifBolumGoster(yardim, btnYardim);
  fetch("/hatter/RegisterSignIn/help/helpList.php")
    .then(res => res.text())
    .then(data => {
      document.getElementById("faq-accordion").innerHTML = data;
      bindFAQEvents();
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
      document.querySelectorAll(".faq-answer").forEach(a => a.style.display = "none");
      document.querySelectorAll(".faq-question").forEach(q => q.classList.remove("active"));
      document.querySelectorAll(".faq-toggle-icon").forEach(i => i.textContent = "+");
      if (!isOpen) {
        answer.style.display = "block";
        question.classList.add("active");
        icon.textContent = "–";
      }
    });
  });
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
