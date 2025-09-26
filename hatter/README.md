## Hatter E-Ticaret Uygulaması

Hatter; PHP (procedural) + MySQL ile geliştirilmiş, ürün listeleme, sepet, sipariş ve blog içerikleri barındıran basit bir e‑ticaret uygulamasıdır. Proje XAMPP/WAMP gibi bir PHP geliştirme ortamında çalışacak şekilde yapılandırılmıştır.

### Özellikler
- Ürün listeleme ve detay sayfaları
- Sepete ekleme/çıkarma, ödeme akışı (checkout)
- Kullanıcı kayıt/giriş, adres yönetimi ve sipariş geçmişi
- Blog gönderileri ve yorum yönetimi
- Yönetim paneli ile ürün/kategori, sipariş, kullanıcı ve sayfa içerik yönetimi

### Gereksinimler
- PHP 7.4+ (PHP 8.x önerilir)
- MySQL/MariaDB
- Apache (XAMPP/WAMP/MAMP önerilir)
- Composer gerekmiyor (projede harici PHP paketi kullanılmıyor)

### Hızlı Başlangıç (Windows + XAMPP)
1. XAMPP kurun ve Apache ile MySQL servislerini başlatın.
2. Bu projeyi `C:\xampp\htdocs\hatter` dizinine yerleştirin.
3. MySQL üzerinde bir veritabanı oluşturun (ör: `hatter`).
4. `sqlBaglanti/db.php` dosyasındaki veritabanı bağlantı bilgilerini kendi ortamınıza göre güncelleyin.
5. Varsayılan tablolar ve veriler için elinizde bir SQL dökümü varsa içe aktarın (proje içinde sağlanmadıysa tabloları kendi ihtiyacınıza göre oluşturun).
6. Tarayıcıdan `http://localhost/hatter/` adresine gidin.

### Klasör Yapısı (Özet)
- `index.php`: Anasayfa
- `AdminPanel/`: Yönetim paneli
  - `admin/panel.php`: Panel ana sayfası
  - `ProductandOrderManagement/`: Ürün/Kategori ve Sipariş yönetimi
  - `UserContentControl/`: Kullanıcı, yorum ve blog içerik yönetimi
  - `ContactandPageSettings/`: İletişim mesajları ve sayfa ayarları
- `RegisterSignIn/`: Kayıt, giriş, kullanıcı hesabı ve adres işlemleri
- `checkout/`: Ödeme akışı
- `cart/`: Sepet işlemleri
- `Blog/`: Blog listesi ve gönderi ekleme
- `sqlBaglanti/db.php`: Veritabanı bağlantısı
- `root/`, `AdminPanel/adminroot/`: Ortak header/footer şablonları
- `uploads/` ve `AdminPanel/.../uploads/`: Yüklenen görseller
- `styles/`, `js/`, `images/`: Statik dosyalar

### Kurulum ve Yapılandırma
- **Veritabanı bağlantısı**: `sqlBaglanti/db.php`
  - Sunucu (host), kullanıcı adı, şifre ve veritabanı adını düzenleyin.
- **Yükleme klasör izinleri**:
  - `uploads/` ve yönetim panelindeki `uploads/` klasörlerinin yazma izni olduğundan emin olun.
- **URL yapısı ve yönlendirme**:
  - Uygulama basit PHP dosya yönlendirmesi kullanır. Ek bir .htaccess gerekli değildir.

### Yönetim Paneli
- Giriş: `http://localhost/hatter/AdminPanel/admin/panel.php`
- Ürün/Kategori: `AdminPanel/ProductandOrderManagement/ProductManagement/`
- Siparişler: `AdminPanel/ProductandOrderManagement/OrderManagement/`
- İletişim Mesajları ve Sayfa Ayarları: `AdminPanel/ContactandPageSettings/`
- Blog ve Yorum Yönetimi: `AdminPanel/UserContentControl/`

Not: Yetkilendirme/kimlik doğrulama akışı proje yapınıza göre özelleştirilebilir. Varsayılan bir admin kullanıcısı sağlanmadıysa veritabanına manuel ekleyin.

### Güvenlik Notları
- Dosya yüklemelerinde MIME türü ve uzantı doğrulaması yapın, maksimum dosya boyutu kısıtlayın.
- Form girdileri için CSRF ve XSS korumaları ekleyin (ör. token, output escaping).
- SQL enjeksiyonuna karşı prepared statements kullanın (mevcut kodları gözden geçirmeniz önerilir).
- Admin paneline ek erişim kontrolleri ve parola karma (password hashing) uygulayın.

### Geliştirme İpuçları
- PHP hatalarını görmek için geliştirme ortamında `display_errors=On` ve `error_reporting(E_ALL)` ayarlayabilirsiniz (üretimde kapatın).
- Ortak şablonlar: `root/header.php`, `root/footer.php` ve yönetim paneli için `AdminPanel/adminroot/` altındaki dosyalar.
- Statik içerik ve resimler için `images/` ve `uploads/` klasörlerini kullanın.

### Yol Haritası (Öneri)
- Formlar için CSRF koruması
- Tüm SQL işlemlerinin prepared statements’a taşınması
- Gelişmiş arama, filtreleme ve sayfalama
- Çoklu dil desteği
- Test verisi ve örnek SQL şeması eklenmesi

### Lisans
Bu proje için belirli bir lisans belirtilmemiştir. Kullanım koşullarını proje sahibine danışın.

### İletişim
Sorular ve katkılar için lütfen proje sahibi ile iletişime geçin veya bir konu (issue) açın.
