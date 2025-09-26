<?php
session_start();

// Admin kontrolü
if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    header("Location: /hatter/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hatter — Homepage</title>
  <link rel="stylesheet" href="/hatter/styles/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    .bg{min-height:100vh; background-size:cover; background-position:center; margin-top:-14.5rem; display:grid; align-items:center}
    .form-row{display:flex; flex-direction:column; gap:.5rem; margin-bottom:1rem;}
    .form-row input,.form-row textarea,.form-row select{padding:.75rem; border-radius:.5rem; border:1px solid #e2e8f0; outline:none}
    .preview{max-width:280px; max-height:160px; object-fit:cover; border:1px solid #e2e8f0; border-radius:.5rem}
    .hint{font-size:1.2rem; color:#64748b}
    .admin-panel-layout {
        display: flex;
        align-items: stretch;     /* yükseklik eşitlensin */
    }
    .admin-sidebar {
        width: 250px;             /* görseldeki gibi */
        flex: 0 0 250px;          /* <-- shrink etme, sabit genişlik */
    }
    .admin-dynamic-area {
        flex: 1 1 auto;
        min-width: 0;             /* içerik taşmasın, tablo genişlerken layout bozmasın */
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
      <h2 class="admin-menu-btn-h3">Homepage</h2>
      <button class="admin-menu-btn" onclick="filterHomepage('add')">Add Category</button>
      <button class="admin-menu-btn" onclick="filterHomepage('list')">List Categories</button>
    </aside>

    <section class="admin-dynamic-area">
      <!-- Açılış -->
      <div id="hp-landing" class="panel-section">
        <img src="/hatter/images/yh.jpg" alt="Background Image" style="width:100%; height:auto; border-radius:1rem;">
      </div>

      <!-- EKLE -->
      <div id="hp-add" class="panel-section" style="display:none;">
        <h2>New Homepage Category</h2>
        <form id="hpForm" enctype="multipart/form-data" style="padding:10px;">
          <div class="form-row" >
            <label for="hp_category_name" >Category Name</label>
            <input type="text" id="hp_category_name" name="category_name" required placeholder="Örn: Yeni Sezon" />
          </div>

          <div class="form-row">
            <label for="hp_sort_order">Number</label>
            <input type="number" id="hp_sort_order" name="sort_order" value="0" />
            <span class="hint">The smaller one appears first.</span>
          </div>
          <div class="form-row">
            <label>
              <input type="checkbox" id="hp_is_active" name="is_active" checked />
              Active
            </label>
          </div>

          <div class="form-row">
            <label for="hp_image_file">Category Image</label>
            <input type="file" id="hp_image_file" name="image_file" accept="image/*" />
            <img id="hp_image_preview" class="preview" alt="Önizleme" style="display:none; margin-top:.5rem;" />
            <span class="hint">JPG/PNG/WEBP • maks 5MB</span>
          </div>

          <button type="submit" class="submit-btn">Save</button>
          <div id="hpSaveResult" style="margin-top:10px; font-weight:600;"></div>
        </form>
      </div>

      <!-- LİSTE -->
      <div id="hp-list" class="panel-section" style="display:none;">
        <h2 id="hp-list-title">Homepage Categories</h2>
        <div id="hpListArea" style="margin-bottom:.6rem;">Loading…</div>
        <table id="hpTable" class="product-table" style="display:none; width:100%;">
          <thead>
            <tr>
              <th>ID</th>
              <th>Category</th>
              <th>Image</th>
              <th>Number</th>
              <th>Active</th>
              <th>Date</th>
              <th>Update</th>
              <th>Transactions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </main>
</div>

<!-- Basit snackbar -->
<div id="snackbar" role="status" aria-live="polite"></div>

<script>
  /* === Sabit kök === */
  const BASE = '/hatter/AdminPanel/ContactandPageSettings/HomepageManagament/';

  /* === Snackbar === */
  function showSnackbar(message, duration = 3000) {
    let bar = document.getElementById('snackbar');
    if (!bar) return;
    bar.classList.remove('show');
    bar.textContent = message;
    bar.classList.add('show');
    clearTimeout(bar.__hideTimer);
    bar.__hideTimer = setTimeout(()=> bar.classList.remove('show'), duration);
  }

  /* === JSON helper === */
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

  /* === Sekme değiştirici (ContactMessages'taki gibi tek giriş) === */
  function filterHomepage(mode){
    // Tüm bölümleri gizle
    document.querySelectorAll('.panel-section').forEach(sec => sec.style.display = 'none');

    if (mode === 'add') {
      document.getElementById('hp-add').style.display = 'block';
      return;
    }
    if (mode === 'list') {
      document.getElementById('hp-list').style.display = 'block';
      loadHomepageList();
      return;
    }

    // default (landing)
    document.getElementById('hp-landing').style.display = 'block';
  }

  /* === Listeyi çek === */
  async function loadHomepageList(){
    const area  = document.getElementById('hpListArea');
    const table = document.getElementById('hpTable');
    area.textContent = 'Yükleniyor…';
    table.style.display = 'none';

    try{
      const data = await fetchJson(BASE + 'homePageGet.php', {
        credentials:'same-origin',
        headers:{'Accept':'application/json'}
      });

      const tbody = table.querySelector('tbody');
      tbody.innerHTML = '';

      (data||[]).forEach(row=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${row.id}</td>
          <td>${row.category_name||''}</td>
          <td>${row.image_url ? `<img src="${row.image_url}" style="width:80px;height:50px;object-fit:cover;border-radius:.5rem;border:1px solid #e5e7eb">` : '-'}</td>
          <td>${row.sort_order ?? ''}</td>
          <td>${Number(row.is_active)===1 ? 'Evet' : 'Hayır'}</td>
          <td>${row.created_at||''}</td>
          <td>${row.updated_at||''}</td>
          <td>
            <button class="delete-btn" onclick="deleteHomepage(${row.id})">
              <i class="fa fa-trash"></i> Sil
            </button>
         </td>
          `;
        tbody.appendChild(tr);
      });

      area.textContent = (data && data.length) ? '' : 'Kayıt yok.';
      table.style.display = (data && data.length) ? 'table' : 'none';
    }catch(err){
      area.textContent = 'Liste yüklenemedi: ' + err.message;
      console.error(err);
      showSnackbar('Liste yüklenemedi', 3500);
    }
  }

  /* === Sayfa hazır olunca === */
  document.addEventListener('DOMContentLoaded', ()=>{
    // Varsayılan bölüm
    filterHomepage();

    // Form submit
    const form = document.getElementById('hpForm');
    form?.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const out = document.getElementById('hpSaveResult');
      out.textContent = 'Kaydediliyor...';
      try{
        const fd = new FormData(form);
        fd.set('is_active', document.getElementById('hp_is_active').checked ? '1' : '0');

        const res = await fetchJson(BASE + 'homepageAdd.php', { method:'POST', body: fd });

        if (res.success) {
          out.textContent = '';
          showSnackbar('Kaydedildi ✅', 2500);
          form.reset();
          const prev = document.getElementById('hp_image_preview');
          if (prev){ prev.style.display='none'; prev.removeAttribute('src'); }
          filterHomepage('list');
        } else {
          const msg = res.message || 'Kaydedilemedi';
          out.textContent = 'Hata: ' + msg;
          showSnackbar('Hata: ' + msg, 3500);
        }
      } catch (err) {
        out.textContent = 'Bir hata oluştu: ' + err.message;
        showSnackbar('Bir hata oluştu: ' + err.message, 3500);
        console.error(err);
      }
    });

    // Görsel önizleme
    const fileInput = document.getElementById('hp_image_file');
    const preview   = document.getElementById('hp_image_preview');
    fileInput?.addEventListener('change', ()=>{
      const f = fileInput.files?.[0];
      if (!f){ preview.style.display='none'; preview.removeAttribute('src'); return; }
      preview.src = URL.createObjectURL(f);
      preview.style.display = 'block';
    });
  });


  async function deleteHomepage(id){
  if (!confirm("Bu kategoriyi silmek istediğinize emin misiniz?")) return;

  try {
    const res = await fetch(BASE + 'homepageDelete.php?id=' + encodeURIComponent(id), {
      method: 'GET',
      credentials: 'same-origin'
    });
    const data = await res.json();

    if(data.success){
      showSnackbar("Silindi ✅", 2500);
      loadHomepageList(); // listeyi yenile
    } else {
      showSnackbar("Silinemedi: " + (data.message || ''), 3500);
    }
  } catch(err){
    console.error(err);
    showSnackbar("Hata: " + err.message, 3500);
  }
}

</script>


<?php include 'C:/xampp/htdocs/hatter/AdminPanel/adminroot/footer.php'; ?>
</body>
</html>
