<?php
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

$sql = "SELECT blog_posts.id, blog_posts.title, blog_posts.image_path, blog_posts.created_at,
blog_posts.content, users.full_name 
FROM blog_posts 
JOIN users ON blog_posts.user_id = users.id 
ORDER BY blog_posts.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();

$output = '<table class="product-table">
<thead>
<tr>
    <th>ID</th>
    <th>Kullanıcı</th>
    <th>Başlık</th>
    <th>Görsel</th>
    <th>Oluşturulma</th>
    <th>İşlem</th>
</tr>
</thead>
<tbody>';

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $full_name = htmlspecialchars($row['full_name']);
        $title = htmlspecialchars($row['title']);
        $img = '/' . htmlspecialchars($row['image_path']);
        $date = $row['created_at'];
        $content = htmlspecialchars($row['content'], ENT_QUOTES); // çok önemli!

        $output .= "<tr>
            <td>$id</td>
            <td>$full_name</td>
            <td>$title</td>
            <td><img src='$img' width='80'></td>
            <td>$date</td>
            <td>
                <button class=\"read-btn\" data-content='" . htmlspecialchars($row['content'], ENT_QUOTES) . "'>📖 Read</button>


                <button onclick='deletePost($id)'>🗑️ Sil</button>
            </td>
        </tr>";
    }
} else {
    $output .= "<tr><td colspan='6'>Hiç post bulunamadı.</td></tr>";
}

$output .= '</tbody></table>';

echo $output;
