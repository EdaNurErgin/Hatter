<?php
declare(strict_types=1);
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// --- Parametreler
$page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage  = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;
$offset   = ($page - 1) * $perPage;

// --- Toplam kayıt
$countSql = "SELECT COUNT(*) AS cnt FROM products";
$total    = (int)$conn->query($countSql)->fetch(PDO::FETCH_ASSOC)['cnt'];
$totalPages = max(1, (int)ceil($total / $perPage));

// --- Kayıtlar (kategori adıyla)
$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id DESC
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
$stmt->execute();

// --- Tablo
$output = '<table class="product-table">
    <thead>
        <tr>
            <th>Product Image</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Explain</th>
            <th>Transactions</th>
        </tr>
    </thead>
    <tbody>';

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $img = htmlspecialchars((string)$row['image']);
        $name = htmlspecialchars((string)$row['name']);
        $price = htmlspecialchars((string)$row['price']);
        $cat = htmlspecialchars((string)($row['category_name'] ?? 'Kategori Yok'));
        $stock = htmlspecialchars((string)$row['stock']);
        $desc = htmlspecialchars((string)$row['description']);
        $id   = (int)$row['id'];

        $output .= "<tr>
            <td><img src='/hatter/{$img}' alt='Ürün Resmi' style='width:80px;height:80px;object-fit:cover;'></td>
            <td>{$name}</td>
            <td>{$price} ₺</td>
            <td>{$cat}</td>
            <td>{$stock}</td>
            <td>{$desc}</td>
            <td>
              <button class='edit-btn' onclick='editProduct({$id})'>✏️ Update</button>
              <button class='delete-btn' onclick='deleteProduct({$id})'>🗑️ Delete</button>
            </td>
        </tr>";
    }
} else {
    $output .= "<tr><td colspan='7'>Hiç ürün bulunamadı.</td></tr>";
}

$output .= '</tbody></table>';

// --- Pagination bar
// Yardımcı: sayfa butonu HTML
// --- Pagination bar (sade: solda Last, ortada sayfa numaraları, sağda Next)
function pageBtn(int $p, int $current): string {
    $active = $p === $current ? 'style="font-weight:600;"' : '';
    return "<button type='button' class='page-btn' {$active} onclick='loadProductPage({$p})'>{$p}</button>";
}

$pagination = '';
if ($totalPages > 1) {
    $pagination .= "<div class='pagination-bar' style='display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-top:1rem;'>";

    // SOLDa: Last (prev)
    if ($page > 1) {
        $pagination .= "<button type='button' class='page-btn' onclick='loadProductPage(" . ($page-1) . ")'>Last</button>";
    } else {
        $pagination .= "<button type='button' class='page-btn' disabled>Last</button>";
    }

    // ORTADA: yalnızca sayfa numaraları (pencere kullanıyoruz, nokta/ellipsis yok)
    $pagination .= "<div class='page-numbers' style='display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;flex:1'>";
    $window = 2; // istersen 1/3 yapabilirsin
    $start  = max(1, $page - $window);
    $end    = min($totalPages, $page + $window);
    for ($i = $start; $i <= $end; $i++) {
        $pagination .= pageBtn($i, $page);
    }
    $pagination .= "</div>";

    // SAĞDA: Next (next)
    if ($page < $totalPages) {
        $pagination .= "<button type='button' class='page-btn' onclick='loadProductPage(" . ($page+1) . ")'>Next</button>";
    } else {
        $pagination .= "<button type='button' class='page-btn' disabled>Next</button>";
    }

    $pagination .= "</div>";
}

// Tablo + Pagination çıktı
echo $output . $pagination;
