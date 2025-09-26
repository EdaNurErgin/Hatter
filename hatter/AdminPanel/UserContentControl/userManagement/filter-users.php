<?php
// Veritabanı bağlantısı
require_once "C:/xampp/htdocs/hatter/sqlBaglanti/db.php";

// Kullanıcıları çek
$sql = "SELECT * FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();

// Başlangıçta tablo başlığı oluştur
$output = '<table class="product-table">
    <thead>
        <tr>
            <th>Ad</th>
            <th>Kullanıcı Adı</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Rol</th>
            <th>İşlemler</th> <!-- İşlemler Kolonu Eklendi -->
        </tr>
    </thead>
    <tbody>';

if ($stmt->rowCount() > 0) {
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Kullanıcı bilgileri
        $full_name = htmlspecialchars($row["full_name"]);
        $username = htmlspecialchars($row["username"]);
        $email = htmlspecialchars($row["email"]);
        $phone = htmlspecialchars($row["phone"]);
        $role = ($row["is_admin"] == 1) ? "Admin" : "Kullanıcı";

        $output .= "<tr>";
        $output .= "<td>" . $full_name . "</td>";
        $output .= "<td>" . $username . "</td>";
        $output .= "<td>" . $email . "</td>";
        $output .= "<td>" . $phone . "</td>";
        $output .= "<td>" . $role . "</td>";
        $output .= "<td>
            
            <button class='delete-btn' onclick='deleteUser(" . $row["id"] . ")'>🗑️ Sil</button>
        </td>";
        $output .= "</tr>";
    }
} else {
    $output .= "<tr><td colspan='6'>Hiç kullanıcı bulunamadı.</td></tr>";  // 6 kolon olacak şimdi
}

$output .= '</tbody></table>';

echo $output;
?>
