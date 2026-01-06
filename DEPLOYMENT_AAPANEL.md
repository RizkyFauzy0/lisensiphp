# Panduan Deployment ke aaPanel - lisensi.gdvmedia.com

## 🚀 Langkah-Langkah Deployment

### 1. Persiapan File
Upload semua file ke directory website aaPanel Anda (biasanya `/www/wwwroot/lisensi.gdvmedia.com/`)

### 2. Konfigurasi Database

#### a. Buat Database di aaPanel
1. Login ke aaPanel
2. Pergi ke **Database** → **Add Database**
3. Buat database baru (misal: `lisensiphp`)
4. Catat username dan password database

#### b. Import Schema
1. Pergi ke **Database** → **phpMyAdmin**
2. Pilih database yang baru dibuat
3. Import file `database/schema.sql`

#### c. Update Konfigurasi Database
Edit file `config/database.php`:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'lisensiphp');        // Sesuaikan dengan nama database Anda
define('DB_USER', 'lisensiphp');        // Sesuaikan dengan username database
define('DB_PASS', 'password_database'); // Sesuaikan dengan password database
define('DB_CHARSET', 'utf8mb4');
```

### 3. Konfigurasi Aplikasi

Edit file `config/config.php`:

```php
<?php
// Application configuration

define('APP_NAME', 'License Management System');
define('APP_URL', 'https://lisensi.gdvmedia.com'); // ⚠️ PENTING: Ubah ke domain Anda
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// API configuration
define('API_RATE_LIMIT', 100); // requests per minute
define('API_KEY_LENGTH', 64);

// Pagination
define('PER_PAGE', 10);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (disable in production) ⚠️ PENTING
error_reporting(0);              // Ubah dari E_ALL
ini_set('display_errors', 0);    // Ubah dari 1
```

### 4. Konfigurasi HTTPS

Edit file `index.php`, ubah baris 5:
```php
ini_set('session.cookie_secure', 1); // ⚠️ PENTING: Ubah dari 0 ke 1 untuk HTTPS
```

### 5. Konfigurasi aaPanel

#### a. Set Document Root
Di aaPanel → **Website** → **Settings** → **Site Directory**
- Document root: `/www/wwwroot/lisensi.gdvmedia.com`
- **JANGAN** set ke subdirectory `/public`

#### b. Enable Rewrite Rules
Di aaPanel → **Website** → **Settings** → **Rewrite**
- Pilih **Custom**
- Pastikan file `.htaccess` sudah ada dan aktif

#### c. PHP Version
Pastikan menggunakan **PHP 7.4** atau lebih tinggi:
- aaPanel → **Website** → **Settings** → **PHP Version**
- Pilih PHP 7.4 atau 8.x

#### d. PHP Extensions
Pastikan extension berikut aktif di aaPanel → **App Store** → **PHP** → **Settings** → **Install Extensions**:
- ✅ PDO
- ✅ pdo_mysql
- ✅ mbstring
- ✅ openssl
- ✅ json

#### e. File Permissions
Set permission yang benar:
```bash
chmod -R 755 /www/wwwroot/lisensi.gdvmedia.com
chmod -R 644 /www/wwwroot/lisensi.gdvmedia.com/*.php
```

### 6. SSL Certificate

⚠️ **PENTING**: Aktifkan SSL untuk domain Anda
1. aaPanel → **Website** → **Settings** → **SSL**
2. Gunakan Let's Encrypt (gratis) atau upload certificate sendiri
3. Enable **Force HTTPS**

---

## ⚠️ Potensi Error & Solusi

### Error 1: 500 Internal Server Error
**Penyebab**: File `.htaccess` tidak terbaca atau mod_rewrite tidak aktif

**Solusi**:
1. Cek `.htaccess` ada di root directory
2. Di aaPanel → **Website** → **Settings** → **Rewrite**, pastikan aktif
3. Cek error log di aaPanel → **Website** → **Log**

### Error 2: Database Connection Error
**Penyebab**: Kredensial database salah atau database tidak exists

**Solusi**:
1. Cek file `config/database.php`
2. Pastikan database sudah dibuat dan schema sudah diimport
3. Test koneksi database di phpMyAdmin

### Error 3: 404 Not Found pada semua route
**Penyebab**: URL rewriting tidak berfungsi

**Solusi**:
1. Pastikan `.htaccess` ada dan readable
2. Cek apache mod_rewrite aktif
3. Coba akses langsung: `https://lisensi.gdvmedia.com/index.php?login`

### Error 4: Session tidak berfungsi
**Penyebab**: Session path tidak writable

**Solusi**:
1. Cek PHP session path: `<?php echo session_save_path(); ?>`
2. Pastikan directory writable: `chmod 777 /tmp` (atau session path)
3. Di aaPanel → **Website** → **Settings** → **PHP**, cek session configuration

### Error 5: API endpoint 404
**Penyebab**: Rewrite rule untuk folder `/api/` tidak berfungsi

**Solusi**:
1. Pastikan file `api/.htaccess` ada
2. Test API endpoint: `https://lisensi.gdvmedia.com/api/validate?api_key=test&domain=test.com`
3. Cek error log

### Error 6: CSS/JS tidak load (Tailwind CSS)
**Penyebab**: Tailwind CSS menggunakan CDN, butuh koneksi internet

**Solusi**: 
- Sudah menggunakan CDN, seharusnya tidak ada masalah
- Cek koneksi internet server
- View source page, pastikan link CDN accessible

### Error 7: "Headers already sent"
**Penyebab**: Output before header() atau BOM di file PHP

**Solusi**:
1. Pastikan tidak ada spasi/newline sebelum `<?php`
2. Save file sebagai UTF-8 without BOM
3. Cek semua file PHP tidak ada `echo` sebelum `header()`

---

## ✅ Checklist Sebelum Go Live

### Database
- [ ] Database sudah dibuat
- [ ] Schema sudah diimport
- [ ] Kredensial database sudah diupdate di `config/database.php`
- [ ] Default admin bisa login (admin/admin123)

### Konfigurasi
- [ ] `APP_URL` sudah diubah ke `https://lisensi.gdvmedia.com`
- [ ] Error reporting sudah disabled (`error_reporting(0)`)
- [ ] `session.cookie_secure` sudah set ke `1`
- [ ] Timezone sudah sesuai

### Server (aaPanel)
- [ ] PHP Version >= 7.4
- [ ] All required PHP extensions installed
- [ ] SSL Certificate installed dan aktif
- [ ] Force HTTPS enabled
- [ ] File permissions correct (755/644)
- [ ] `.htaccess` files exists dan readable

### Testing
- [ ] Buka `https://lisensi.gdvmedia.com` → redirect ke login
- [ ] Login dengan admin/admin123 berhasil
- [ ] Bisa buat lisensi baru
- [ ] API endpoint berfungsi: `/api/validate`
- [ ] Semua route berfungsi (dashboard, licenses, users)

### Security
- [ ] Ganti password default admin
- [ ] Delete atau rename folder `database/` (opsional, untuk keamanan)
- [ ] Backup database
- [ ] Set firewall rules jika perlu

---

## 🔧 Testing Deployment

### 1. Test Homepage
```
https://lisensi.gdvmedia.com
```
Harus redirect ke `/login`

### 2. Test Login
- Username: `admin`
- Password: `admin123`

### 3. Test API Endpoint
```bash
curl -X POST "https://lisensi.gdvmedia.com/api/validate" \
  -d "api_key=YOUR_API_KEY" \
  -d "domain=test.com"
```

Atau buka di browser:
```
https://lisensi.gdvmedia.com/api/validate?api_key=test&domain=test
```

Response harus JSON (walau invalid API key):
```json
{
  "status": "invalid",
  "message": "API key tidak valid"
}
```

---

## 🆘 Troubleshooting

### Cek Error Log
```bash
# Di aaPanel
Website → Settings → Log → Error Log
```

### Cek PHP Info
Buat file `info.php` di root:
```php
<?php phpinfo(); ?>
```
Akses: `https://lisensi.gdvmedia.com/info.php`

⚠️ **HAPUS file ini setelah selesai cek!**

### Enable Debug Mode (Sementara)
Edit `config/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

⚠️ **Jangan lupa disable lagi setelah selesai debug!**

---

## 📞 Support

Jika masih ada error setelah mengikuti panduan ini, cek:
1. Error log di aaPanel
2. PHP error log
3. Browser console untuk error JavaScript/CSS

---

## 🎯 Quick Start (TL;DR)

```bash
# 1. Upload files ke aaPanel
# 2. Buat database & import schema.sql
# 3. Edit config/database.php
# 4. Edit config/config.php (ubah APP_URL & disable error reporting)
# 5. Edit index.php (set session.cookie_secure = 1)
# 6. Set PHP >= 7.4
# 7. Install SSL certificate
# 8. Test: https://lisensi.gdvmedia.com
```

**Seharusnya tidak ada error jika semua langkah diikuti dengan benar!** ✅
