# License Management System (LisensiPHP)

Aplikasi manajemen lisensi berbasis web lengkap menggunakan PHP Native dengan arsitektur MVC dan Tailwind CSS yang responsif.

## 🚀 Fitur Utama

### 1. **Sistem Autentikasi Multi-User Admin**
- Login/Register admin dengan validasi
- Role management (super admin & admin biasa)
- Session management yang aman
- Password hashing dengan bcrypt

### 2. **Manajemen Domain/Lisensi (CRUD)**
- Tambah domain baru
- Edit informasi domain
- Hapus domain
- List semua domain dengan pagination
- Search/filter domain

### 3. **Generate API Key**
- Generate API key unik secara otomatis (64 karakter)
- Format aman menggunakan random_bytes
- Opsi regenerate API key

### 4. **Expiry Date untuk Lisensi**
- Set tanggal kadaluarsa lisensi per domain
- Notifikasi untuk lisensi yang akan expired (7 hari)
- Status lisensi (active, expired, suspended)
- Auto-update status expired

### 5. **Limit Jumlah Request API**
- Set limit request API per domain
- Tracking penggunaan request real-time
- Progress bar visual
- Block otomatis jika melebihi limit
- Reset request count

### 6. **Log Aktivasi**
- Catat setiap aktivasi/validasi lisensi
- Log IP address, timestamp, status
- History aktivasi per domain dengan pagination
- Statistik aktivitas 30 hari terakhir

### 7. **API Endpoint untuk Validasi**
- Endpoint: `POST/GET /api/validate`
- Validasi domain + API key
- Response JSON lengkap
- Rate limiting berdasarkan request count

### 8. **Dashboard Admin**
- Overview statistik (total lisensi, active, expired)
- Warning untuk lisensi akan expired
- Recent activities log
- Quick actions

## 📁 Struktur Folder

```
lisensiphp/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── LicenseController.php
│   │   ├── UserController.php
│   │   └── ApiController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── License.php
│   │   ├── ApiLog.php
│   │   └── Database.php
│   └── views/
│       ├── layouts/
│       │   └── main.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── dashboard/
│       │   └── index.php
│       ├── licenses/
│       │   ├── index.php
│       │   ├── create.php
│       │   ├── edit.php
│       │   └── show.php
│       └── users/
│           ├── index.php
│           └── edit.php
├── config/
│   ├── config.php
│   └── database.php
├── api/
│   ├── index.php
│   └── .htaccess
├── database/
│   └── schema.sql
├── .htaccess
├── index.php
└── README.md
```

## 💻 Teknologi

- **Backend**: PHP 7.4+ (Native)
- **Database**: MySQL/MariaDB
- **Frontend**: Tailwind CSS 3.x (via CDN)
- **JavaScript**: Alpine.js 3.x untuk interaktivitas
- **Icons**: Font Awesome 6.4

## 🛠️ Instalasi

### Persyaratan
- PHP 7.4 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.2+
- Apache dengan mod_rewrite enabled
- Extension PHP: PDO, pdo_mysql

### Langkah Instalasi

1. **Clone Repository**
```bash
git clone https://github.com/RizkyFauzy0/lisensiphp.git
cd lisensiphp
```

2. **Konfigurasi Database**

Buat database baru:
```sql
CREATE DATABASE lisensiphp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import schema:
```bash
mysql -u root -p lisensiphp < database/schema.sql
```

3. **Konfigurasi Aplikasi**

Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'lisensiphp');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Edit file `config/config.php` dan sesuaikan `APP_URL`:
```php
define('APP_URL', 'http://localhost/lisensiphp');
```

4. **Setup Virtual Host (Opsional)**

Contoh konfigurasi Apache:
```apache
<VirtualHost *:80>
    ServerName license.local
    DocumentRoot "/path/to/lisensiphp"
    
    <Directory "/path/to/lisensiphp">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Tambahkan ke `/etc/hosts`:
```
127.0.0.1 license.local
```

5. **Akses Aplikasi**

Buka browser dan akses:
```
http://localhost/lisensiphp
```

Default login:
- **Username**: admin
- **Password**: admin123

## 📚 Penggunaan

### Login Admin
1. Akses halaman login
2. Masukkan username dan password
3. Klik "Masuk"

### Menambah Lisensi Baru
1. Login sebagai admin
2. Klik menu "Lisensi"
3. Klik tombol "Tambah Lisensi"
4. Isi form:
   - **Domain**: example.com (tanpa http/https)
   - **Status**: active/suspended/expired
   - **Limit Request**: jumlah maksimal request (default: 1000)
   - **Tanggal Kadaluarsa**: opsional
5. Klik "Simpan Lisensi"
6. API Key akan di-generate otomatis

### Mengelola Lisensi
- **View Detail**: Klik icon mata pada daftar lisensi
- **Edit**: Klik icon edit untuk mengubah informasi
- **Delete**: Klik icon trash dan konfirmasi penghapusan
- **Regenerate API Key**: Buka detail lisensi, klik tombol "Regenerate API Key"
- **Reset Request Count**: Buka detail lisensi, klik tombol "Reset Request Count"

### Manajemen User (Super Admin)
Hanya super admin yang dapat mengelola user:
1. Klik menu "Users"
2. Edit atau hapus user yang ada
3. Ubah role user (admin/super_admin)
4. Update password user

## 🔌 API Documentation

### Endpoint Validasi Lisensi

**URL**: `/api/validate`

**Method**: `GET` atau `POST`

**Parameters**:
- `api_key` (required): API key dari lisensi
- `domain` (required): Domain yang akan divalidasi

**Response Success** (200):
```json
{
    "status": "valid",
    "message": "Lisensi valid",
    "data": {
        "domain": "example.com",
        "expires_at": "2024-12-31",
        "remaining_days": 120,
        "request_count": 50,
        "request_limit": 1000,
        "remaining_requests": 950
    }
}
```

**Response Invalid** (401/403):
```json
{
    "status": "invalid",
    "message": "API key tidak valid"
}
```

**Response Blocked** (429):
```json
{
    "status": "blocked",
    "message": "Limit request API sudah tercapai",
    "request_count": 1000,
    "request_limit": 1000
}
```

## 🔗 Integrasi di Aplikasi Klien

### PHP Example

```php
<?php
// Konfigurasi
$api_key = 'YOUR_API_KEY_HERE';
$domain = $_SERVER['HTTP_HOST'];
$license_server = 'http://license.local';

// Validasi lisensi
function validateLicense($api_key, $domain, $license_server) {
    $url = "$license_server/api/validate?api_key=$api_key&domain=$domain";
    
    $response = @file_get_contents($url);
    if ($response === false) {
        return ['status' => 'error', 'message' => 'Tidak dapat terhubung ke server lisensi'];
    }
    
    return json_decode($response, true);
}

// Cek lisensi
$result = validateLicense($api_key, $domain, $license_server);

if ($result['status'] !== 'valid') {
    die('Lisensi tidak valid: ' . $result['message']);
}

// Aplikasi berjalan normal
echo "Lisensi valid! Sisa request: " . $result['data']['remaining_requests'];
?>
```

### cURL Example

```php
<?php
$api_key = 'YOUR_API_KEY_HERE';
$domain = 'example.com';
$license_server = 'http://license.local';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$license_server/api/validate");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'api_key' => $api_key,
    'domain' => $domain
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$result = json_decode($response, true);
curl_close($ch);

if ($result['status'] !== 'valid') {
    die('Lisensi tidak valid: ' . $result['message']);
}
?>
```

### JavaScript Example

```javascript
async function validateLicense(apiKey, domain) {
    const url = `http://license.local/api/validate?api_key=${apiKey}&domain=${domain}`;
    
    try {
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.status !== 'valid') {
            console.error('Lisensi tidak valid:', result.message);
            return false;
        }
        
        console.log('Lisensi valid!', result.data);
        return true;
    } catch (error) {
        console.error('Error validasi lisensi:', error);
        return false;
    }
}

// Penggunaan
validateLicense('YOUR_API_KEY', 'example.com');
```

## 🔒 Keamanan

### Best Practices
1. **Password**: Gunakan password yang kuat (minimal 8 karakter)
2. **HTTPS**: Gunakan HTTPS di production untuk enkripsi data
3. **Database**: Ganti password database default
4. **API Key**: Simpan API key dengan aman, jangan commit ke repository
5. **File Permissions**: Set permission yang tepat (755 untuk folder, 644 untuk file)
6. **Error Reporting**: Disable di production (set `display_errors = 0`)

### Security Features
- Password hashing dengan bcrypt
- SQL Injection prevention (prepared statements)
- XSS protection (htmlspecialchars)
- CSRF protection via session
- Input validation
- Security headers (.htaccess)

## 📊 Database Schema

### Table: users
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Table: licenses
```sql
CREATE TABLE licenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    domain VARCHAR(255) NOT NULL,
    api_key VARCHAR(64) UNIQUE NOT NULL,
    status ENUM('active', 'expired', 'suspended') DEFAULT 'active',
    request_limit INT DEFAULT 1000,
    request_count INT DEFAULT 0,
    expires_at DATE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Table: api_logs
```sql
CREATE TABLE api_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_id INT,
    ip_address VARCHAR(45),
    request_domain VARCHAR(255),
    status ENUM('success', 'failed', 'blocked') NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (license_id) REFERENCES licenses(id)
);
```

## 🐛 Troubleshooting

### 404 Error di Semua Halaman
- Pastikan mod_rewrite Apache sudah enabled
- Check file `.htaccess` ada di root folder
- Restart Apache

### Database Connection Error
- Check kredensial database di `config/database.php`
- Pastikan MySQL service running
- Pastikan database sudah dibuat dan schema sudah diimport

### Session Issues
- Check PHP session configuration
- Pastikan folder session writable
- Clear browser cookies

### API Tidak Bisa Diakses
- Check `.htaccess` di folder `api/`
- Pastikan URL rewrite bekerja
- Check error log Apache

## 📝 Changelog

### Version 1.0.0 (2024)
- Initial release
- Fitur dasar manajemen lisensi
- API validation endpoint
- Dashboard dengan statistik
- Log aktivitas
- Multi-user dengan role management

## 👨‍💻 Developer

- **Nama**: RizkyFauzy0
- **Repository**: [https://github.com/RizkyFauzy0/lisensiphp](https://github.com/RizkyFauzy0/lisensiphp)

## 📄 License

This project is open-source and available under the MIT License.

## 🤝 Kontribusi

Kontribusi selalu welcome! Silakan:
1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📧 Support

Jika ada pertanyaan atau issues, silakan buat issue baru di GitHub repository.

---

**Happy Coding! 🚀**
