<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] != 1) {
    header("Location: /hatter/index.php");
    exit;
}

require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php"; // $conn (PDO) bekleniyor

// --- Parametreler
$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage  = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
$offset   = ($page - 1) * $perPage;

// --- Toplam kayıt
$countSql = "SELECT COUNT(*) AS cnt FROM categories";
$total    = (int)$conn->query($countSql)->fetch(PDO::FETCH_ASSOC)['cnt'];
$totalPages = max(1, (int)ceil($total / $perPage));

// --- Kayıtlar
$sql = "SELECT id, name, slug, image
        FROM categories
        ORDER BY id DESC
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();

// --- Tablo HTML
$output = '<table class="product-table">
    <thead>
        <tr>
            <th>Category Image</th>
            <th>Category Name</th>
            <th>Slug</th>
            <th>Transactions</th>
        </tr>
    </thead>
    <tbody>';

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id   = (int)$row['id'];
        $name = htmlspecialchars((string)$row['name']);
        $slug = htmlspecialchars((string)$row['slug']);
        $imgPath = (string)($row['image'] ?? '');
        $img = $imgPath !== '' ? "/hatter/" . ltrim($imgPath, "/") : "/hatter/uploads/default-category.png";
        $imgEsc = htmlspecialchars($img);

        $output .= "<tr>
            <td><img src='{$imgEsc}' alt='Kategori' style='width:80px;height:80px;object-fit:cover;'></td>
            <td>{$name}</td>
            <td>{$slug}</td>
            <td>
                <button class='edit-btn' onclick='editCategory({$id})'>✏️ Update</button>
                <button class='delete-btn' onclick='deleteCategory({$id})'>🗑️ Delete</button>
            </td>
        </tr>";
    }
} else {
    $output .= "<tr><td colspan='4'>Hiç kategori bulunamadı.</td></tr>";
}
$output .= '</tbody></table>';

// --- Pagination helpers
function pageBtn(int $p, int $current): string {
    $active = $p === $current ? 'style="font-weight:600;"' : '';
    return "<button type='button' class='page-btn' {$active} onclick='loadCategoryPage({$p})'>{$p}</button>";
}

// --- Pagination bar
$pagination = '';
if ($totalPages > 1) {
    $pagination .= "<div class='pagination-bar' style='display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:1rem;'>";

    // LEFT: Last (prev)
    if ($page > 1) {
        $pagination .= "<button type='button' class='page-btn' onclick='loadCategoryPage(" . ($page-1) . ")'>Last</button>";
    } else {
        $pagination .= "<button type='button' class='page-btn' disabled>Last</button>";
    }

    // CENTER: page numbers (window)
    $pagination .= "<div class='page-numbers' style='display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;flex:1'>";
    $window = 2; // 1/2/3 yapabilirsin
    $start  = max(1, $page - $window);
    $end    = min($totalPages, $page + $window);
    for ($i = $start; $i <= $end; $i++) {
        $pagination .= pageBtn($i, $page);
    }
    $pagination .= "</div>";

    // RIGHT: Next (next)
    if ($page < $totalPages) {
        $pagination .= "<button type='button' class='page-btn' onclick='loadCategoryPage(" . ($page+1) . ")'>Next</button>";
    } else {
        $pagination .= "<button type='button' class='page-btn' disabled>Next</button>";
    }

    $pagination .= "</div>";
}

// Çıktı
echo $output . $pagination;
