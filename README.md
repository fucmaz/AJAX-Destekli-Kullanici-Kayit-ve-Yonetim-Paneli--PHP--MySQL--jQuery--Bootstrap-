# AJAX Kayıt ve Yönetim Sistemi (PHP + MySQL + jQuery + Bootstrap)

Bu proje, AJAX kullanarak kullanıcı kayıt işlemi yapan, e-posta doğrulaması ile güvenlik sağlayan, admin paneli üzerinden kullanıcı yönetimi (listeleme, düzenleme, silme) ve rol bazlı yetkilendirme sunan tam işlevsel bir sistemdir.

---

## 🇹🇷 Özellikler (Türkçe)

- ✅ AJAX ile kayıt (sayfa yenilemeden)
- ✅ E-posta doğrulama (PHPMailer ile, süresi dolan bağlantılar)
- ✅ Şifreleme (`password_hash`) ve oturum yenileme
- ✅ CSRF, güvenlik başlıkları, giriş/kayıt hız sınırlama
- ✅ Giriş modülü
- ✅ Rol bazlı yetkilendirme (admin, editor, user)
- ✅ Admin paneli:
  - Kullanıcı listeleme
  - Düzenleme (ad, soyad, durum, rol)
  - Silme (admin veya kendisi silinemez)
- ✅ SweetAlert ile modern bildirimler
- ✅ Bootstrap 5 ile responsive tasarım

---

## 🇬🇧 Features (English)

- ✅ AJAX-based registration (without reload)
- ✅ Email verification (via PHPMailer, expiring links)
- ✅ Secure password hashing and session regeneration
- ✅ CSRF protection, security headers, login/register rate limits
- ✅ Login system
- ✅ Role-based authorization (admin, editor, user)
- ✅ Admin panel:
  - User listing
  - Edit (name, status, role)
  - Delete (cannot delete admins or self)
- ✅ SweetAlert feedback
- ✅ Responsive layout with Bootstrap 5

---

## 🛠 Kurulum / Setup

1. Veritabanı oluşturun: `ajax_kayit`
2. Yeni kurulumda `sql/tablo.sql` dosyasını çalıştırın. Mevcut kurulum için `sql/guncelle_guvenlik.sql` dosyasını kullanın.
3. `config.local.example.php` dosyasını `config.local.php` olarak kopyalayıp veritabanı ve SMTP ayarlarını doldurun.
4. Tarayıcıda `index.php` üzerinden kayıt işlemini başlatın.

```bash
cp ajax-kayit-sistemi/config.local.example.php ajax-kayit-sistemi/config.local.php
```

---

## 📁 Klasör Yapısı / Folder Structure

```
ajax-kayit-sistemi/
├── bootstrap.php
├── config.local.example.php
├── index.php
├── login.php
├── panel.php
├── kullanici_listesi.php
├── kullanici_duzenle.php
├── kullanici_sil.php
├── kullanici_guncelle.php
├── dogrula.php
├── kaydet.php
├── giris_kontrol.php
├── logout.php
├── db.php
├── includes/
│   ├── header.php
│   ├── auth.php
│   ├── security.php
│   └── ...
├── js/
│   ├── app.js
│   ├── main.js
│   ├── login.js
│   └── kullanici.js
├── sql/
│   ├── tablo.sql
│   └── guncelle_guvenlik.sql
├── src/
│   └── phpmailer/
└── tests/
    └── run.php
```

---

## 👥 Rol Açıklamaları / Role Descriptions

| Rol     | Açıklama (TR)                                | Description (EN)                         |
|---------|-----------------------------------------------|-------------------------------------------|
| admin   | Tüm sistem erişimi (kullanıcı yönetimi dahil) | Full access including user management     |
| editor  | İçerik ekleyebilir / silebilir                | Can upload/delete content                 |
| user    | Sadece görebilir ve favorileyebilir          | View and favorite only                    |

Kullanıcı listesi, düzenleme ve silme yalnızca `admin` rolüne açıktır.

---

## 🔐 Demo Admin Hesabı / Admin Demo Account

> E-posta: `info@deneme.com.tr`  
> Şifre: `ChangeMe!123`

Kurulumdan sonra bu şifreyi mutlaka değiştirin.

---

## 🧪 Testler / Tests

```bash
php ajax-kayit-sistemi/tests/run.php
```

---

## 🧾 Lisans / License

MIT Lisansı ile açık kaynak olarak yayınlanmıştır.  
Free to use under the MIT License.
