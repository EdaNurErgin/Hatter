<?php
session_start();
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    header("Location: /hatter/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hatter — About Us</title>
  <link rel="stylesheet" href="/hatter/styles/style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <style>
    .bg{min-height:100vh; background-size:cover; background-position:center; margin-top:-14.5rem; display:grid; align-items:center}
    .form-row{display:flex; flex-direction:column; gap:.5rem; margin-bottom:1rem;}
    .form-row input,.form-row textarea{padding:.75rem; border-radius:.5rem; border:1px solid #e2e8f0; outline:none}
    .admin-dynamic-area h2{margin-bottom:1rem}
    #aboutSaveResult{font-weight:600}
    .preview{max-width:280px; max-height:160px; object-fit:cover; border:1px solid #e2e8f0; border-radius:.5rem}
    /* snackbar varyantları */
    #snackbar.success { border-left-color:#22c55e; }
    #snackbar.error   { border-left-color:#ef4444; }
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
      <h2 class="admin-menu-btn-h3">About Us Editing</h2>
      <button class="admin-menu-btn" onclick="showSection('about-form')">About Us Change</button>
    </aside>

    <section class="admin-dynamic-area">
      <div id="orders" class="panel-section">
        <img id="heroImage" src="/hatter/images/yh.jpg" alt="Background Image">
      </div>

      <div id="about-form" class="panel-section" style="display:none;">
        <h2 id="about-form-title">About Us  Update</h2>

        <form id="aboutForm" enctype="multipart/form-data" style="padding:10px;">
          <input type="hidden" name="id" id="about_id">

          <div class="form-row">
            <label for="about_title">Title</label>
            <input type="text" id="about_title" name="title" required/>
          </div>

          <div class="form-row">
            <label for="about_subtitle">Subheading</label>
            <input type="text" id="about_subtitle" name="subtitle"/>
          </div>

          <div class="form-row">
            <label for="about_image_file">Image Download</label>
            <input type="file" id="about_image_file" name="image_file" accept="image/*"/>
            <img id="about_image_preview" class="preview" alt="Önizleme" style="display:none"/>
            <input type="hidden" id="current_image_url" name="current_image_url"/>
          </div>

          <div class="form-row">
            <label for="about_content1">Content 1</label>
            <textarea id="about_content1" name="content1" rows="6" required></textarea>
          </div>

          <div class="form-row">
            <label for="about_content2">Content 2</label>
            <textarea id="about_content2" name="content2" rows="6"></textarea>
          </div>

          <div class="form-row">
            <label for="about_content3">Content 3</label>
            <textarea id="about_content3" name="content3" rows="6"></textarea>
          </div>

          <button type="submit" class="submit-btn">Save</button>
          <div id="aboutSaveResult" style="margin-top:10px;"></div>
        </form>

      </div>
    </section>
  </main>
</div>

<!-- snackbar -->
<div id="snackbar" role="status" aria-live="polite"></div>

<script>
// === Yardımcılar ===
function showSnackbar(message, duration = 3000, type = 'success') {
  const bar = document.getElementById('snackbar');
  if (!bar) return;
  bar.classList.remove('show','success','error');
  bar.textContent = message;
  if (type) bar.classList.add(type);
  bar.classList.add('show');
  clearTimeout(bar.__hideTimer);
  bar.__hideTimer = setTimeout(()=> bar.classList.remove('show'), duration);
}

async function fetchJson(url, opts = {}) {
  const r = await fetch(url, opts);
  const ct = r.headers.get('content-type') || '';
  if (!r.ok) {
    const t = await r.text().catch(()=> '');
    throw new Error(`HTTP ${r.status} ${r.statusText} | ${t}`);
  }
  if (!ct.includes('application/json')) {
    const t = await r.text().catch(()=> '');
    throw new Error('JSON bekleniyordu, gelen: ' + ct + ' | ' + t);
  }
  return r.json();
}

// Bulunduğun sayfanın tam URL’sini baz al (href) → 404 azalır
const HERE = new URL(window.location.href);

// Paneli gösterirken veriyi çek
function showSection(id){
  document.querySelectorAll('.panel-section').forEach(s=>s.style.display='none');
  document.getElementById(id).style.display='block';

  if(id==='about-form'){
    const getUrl = new URL('get.php', HERE).toString();
    fetchJson(getUrl, { credentials: 'same-origin', headers:{'Accept':'application/json'} })
      .then(d=>{
        document.getElementById('about_id').value          = d?.id ?? '';
        document.getElementById('about_title').value       = d?.title ?? '';
        document.getElementById('about_subtitle').value    = d?.subtitle ?? '';
        document.getElementById('about_content1').value    = d?.content1 ?? '';
        document.getElementById('about_content2').value    = d?.content2 ?? '';
        document.getElementById('about_content3').value    = d?.content3 ?? '';
        document.getElementById('current_image_url').value = d?.image_url ?? '';

        const prev = document.getElementById('about_image_preview');
        if (d?.image_url) { prev.src = d.image_url; prev.style.display = 'block'; }
        else { prev.removeAttribute('src'); prev.style.display = 'none'; }
      })
      .catch(err=>{
        document.getElementById('aboutSaveResult').innerText = 'Form verileri yüklenemedi: ' + err.message;
        console.error(err);
        showSnackbar('Form verileri yüklenemedi', 3500, 'error');
      });
  }
}

document.addEventListener('DOMContentLoaded', ()=>{
  const form = document.getElementById('aboutForm');

  form?.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const result = document.getElementById('aboutSaveResult');
    result.innerText = 'Kaydediliyor...';

    try {
      const fd = new FormData(form);
      const postUrl = new URL('update.php', HERE).toString();
      const res = await fetchJson(postUrl, { method:'POST', body: fd });

      if (res.success) {
        result.innerText = '';
        showSnackbar('Kaydedildi ✅', 2500, 'success');

        const url = res.image_url || document.getElementById('current_image_url').value;
        if (url) {
          document.getElementById('current_image_url').value = url;
          const img = document.getElementById('heroImage');
          if (img) img.src = url;
        }
      } else {
        const msg = res.message || 'Kaydedilemedi';
        result.innerText = 'Hata: ' + msg;
        showSnackbar('Hata: ' + msg, 3500, 'error');
      }
    } catch (err) {
      result.innerText = 'Bir hata oluştu: ' + err.message;
      console.error(err);
      showSnackbar('Bir hata oluştu: ' + err.message, 3500, 'error');
    }
  });

  // Dosya önizleme
  const fileInput = document.getElementById('about_image_file');
  const preview   = document.getElementById('about_image_preview');
  fileInput?.addEventListener('change', ()=>{
    const f = fileInput.files?.[0];
    if (!f){ preview.style.display='none'; preview.removeAttribute('src'); return; }
    preview.src = URL.createObjectURL(f);
    preview.style.display = 'block';
  });
});
</script>

<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>
</body>
</html>
