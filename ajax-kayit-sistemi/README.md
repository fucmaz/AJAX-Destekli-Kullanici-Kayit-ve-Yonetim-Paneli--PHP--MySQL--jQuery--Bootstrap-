# AJAX Kayıt ve Yönetim Sistemi (PHP + MySQL + jQuery + Bootstrap)

Bu proje, AJAX kullanarak kullanıcı kayıt işlemi yapan, e-posta doğrulaması ile güvenlik sağlayan, admin paneli üzerinden kullanıcı yönetimi (listeleme, düzenleme, silme) ve rol bazlı yetkilendirme sunan tam işlevsel bir sistemdir.

---

## 🇹🇷 Özellikler (Türkçe)

- ✅ AJAX ile kayıt (sayfa yenilemeden)
- ✅ E-posta doğrulama (PHPMailer ile)
- ✅ Şifreleme (password_hash)
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
- ✅ Email verification (via PHPMailer)
- ✅ Secure password hashing
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

1. Veritabanı oluştur: `ajax_kayit`
2. `sql` klasöründeki SQL dosyasını çalıştır.
3. `db.php` içindeki bağlantı ayarlarını düzenle.
4. E-posta gönderimi için PHPMailer ayarlarını `kaydet.php` içinde yap.
5. Tarayıcıda `index.php` üzerinden kayıt işlemini başlat.

---

## 📁 Klasör Yapısı / Folder Structure

ajax-kayit-sistemi/
├── index.php
├── login.php
├── panel.php
├── kullanici_listesi.php
├── kullanici_duzenle.php
├── kullanici_sil.php
├── kullanici_guncelle.php
├── dogrula.php
├── kaydet.php
├── db.php
├── includes/
│ ├── header.php
│ └── auth.php
├── js/
│ └── main.js
├── sql/
│ └── tablo.sql
├── src/
│ └── PHPMailer (klasörü)


---

## 👥 Rol Açıklamaları / Role Descriptions

| Rol     | Açıklama (TR)                                | Description (EN)                         |
|---------|-----------------------------------------------|-------------------------------------------|
| admin   | Tüm sistem erişimi (kullanıcı yönetimi dahil) | Full access including user management     |
| editor  | İçerik ekleyebilir / silebilir                | Can upload/delete content                 |
| user    | Sadece görebilir ve favorileyebilir          | View and favorite only                    |

---

## 🔐 Demo Admin Hesabı / Admin Demo Account

> E-posta: `info@deneme.com.tr`  
> Şifre: `123456` *(password_hash ile şifrelenmiş)*

---

## 🧾 Lisans / License

MIT Lisansı ile açık kaynak olarak yayınlanmıştır.  
Free to use under the MIT License.

---

