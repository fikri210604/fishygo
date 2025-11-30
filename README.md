
# 🐟 **E-Commerce Ikan Segar**

**Project Akhir Pemrograman Web Lanjut 2 – Laravel 10**

Aplikasi E-Commerce Ikan Segar merupakan sebuah platform penjualan produk perikanan secara online yang dikembangkan menggunakan Laravel 10 sebagai framework utama. Sistem ini dirancang untuk mendigitalisasi proses jual-beli ikan segar, mulai dari tahap penelusuran produk hingga tahap transaksi dan pengiriman, sehingga memberikan pengalaman belanja yang lebih cepat, mudah, dan terstruktur bagi pengguna.

---

Penjelasan Proyek (9 poin)

1) Judul Proyek
- Nama: E-Commerce Ikan Segar
- Deskripsi: Platform e-commerce untuk katalog, keranjang, checkout, pembayaran (Midtrans/COD), dan manajemen pesanan produk perikanan.

2) Deskripsi Singkat Proyek
- Fungsi: Menyediakan katalog produk, keranjang, checkout dengan ongkir, pembayaran, riwayat pesanan, dan panel admin.
- Pengguna: Admin, User (pelanggan), Kurir.
- Masalah yang diselesaikan: Digitalisasi jual-beli ikan, perhitungan ongkir otomatis, pembayaran online, dan pengelolaan pesanan terstruktur.

3) Peran (Role)
- Role minimal: Admin, User, Kurir.
- Perbedaan akses:
  - Admin: akses dashboard admin, CRUD master data (produk, kategori, jenis ikan), manajemen user, role & permission, pesanan, artikel. Rute dilindungi gate `can:access-admin` di `routes/web.php:75`, `routes/web.php:82` dan didefinisikan di `app/Providers/AuthServiceProvider.php:24`.
  - Kurir: akses dashboard kurir, melihat pesanan untuk dikirim. Rute dilindungi gate `can:access-kurir` di `routes/web.php:79`, definisi gate di `app/Providers/AuthServiceProvider.php:36`.
  - User: belanja, keranjang, checkout, riwayat pesanan, ulasan. Akses melalui middleware `auth` dan beberapa halaman `verified`.
- Permission dinamis: `Gate::before` memeriksa permission berbasis role di `app/Providers/AuthServiceProvider.php:16`.

4) Autentikasi & Verifikasi
- Login: Laravel Breeze. Login memakai `Auth::attempt(...)` dan session regeneration di `app/Http/Controllers/Auth/AuthenticatedSessionController.php:26`. Password di-hash saat registrasi dengan `Hash::make(...)` di `app/Http/Controllers/Auth/RegisteredUserController.php:147`.
- Verifikasi email: model `User` `implements MustVerifyEmail` (`app/Models/User.php:6`), middleware `verified` pada grup rute admin (`routes/web.php:73`), dan alur verifikasi di `routes/auth.php:33`.
- Login Google (SSO): tersedia via Socialite (`routes/auth.php:24`).

5) Validasi (View & Controller)
- View: menampilkan error dan nilai lama (contoh `resources/views/admin/articles/_form.blade.php:9`, `resources/views/checkout.blade.php:64`).
- Controller/Request:
  - Login: `app/Http/Requests/Auth/LoginRequest.php` memvalidasi email/password dan rate limiting.
  - Checkout: `app/Http/Requests/CheckoutStoreRequest.php` memvalidasi metode pembayaran/catatan.
  - Batal pesanan: `app/Http/Requests/OrderCancelRequest.php` memvalidasi alasan/catatan.
  - Produk: validasi lengkap pada `store`/`update` (unik `kode_produk`, `slug`, tanggal promo, stok >= 0) di `app/Http/Controllers/Admin/ProdukController.php:31` dan `app/Http/Controllers/Admin/ProdukController.php:146`.
- Validasi status pesanan: hanya bisa dibatalkan pada status tertentu di `app/Models/Pesanan.php:53` dan layanan terkait di `app/Services/PesananService.php`.

6) Session & Otorisasi
- Session: diregenerasi saat login dan di-invalidasi saat logout (`AuthenticatedSessionController`). Keranjang via session + cookie persist (`app/Services/CartService.php`).
- Otorisasi: middleware `auth`, `verified`, gate `can:...`. Gate role (`access-admin`, `access-kurir`, `access-user`) di `app/Providers/AuthServiceProvider.php`, plus `Gate::before` untuk permission.

7) Data Master
- Kategori Produk: `database/migrations/2025_11_01_125222_create_table_kategori_produk.php`.
- Jenis Ikan: `database/migrations/2025_11_01_125244_create_table_jenis_ikan.php`.
- Produk (+ foto): `database/migrations/2025_11_01_125309_create_table_produk.php`, `2025_11_17_000000_create_produk_foto_table.php`.
- Artikel: `database/migrations/2025_10_28_010000_create_articles_table.php`.
- Akun/Alamat: `2025_09_30_051341_create_penggunas_table.php`, `2025_11_01_125147_create_table_alamat.php`.
- Role & Permission: `2025_11_05_184700_create_permisson_table.php` (roles, permissions, pivot).
- Transaksi: `2025_11_06_100000_create_pesanan_table.php`, `100100_create_pesanan_item_table.php`, `100200_create_pembayaran_table.php`, `100300_create_pengiriman_table.php`, `100500_create_log_pesanan_table.php`.
- Keranjang (server-side): `2025_11_06_100400_create_keranjang_table.php`, `100410_create_keranjang_item_table.php`.

8) Fitur Tambahan
- API eksternal wilayah (RajaOngkir/kompatibel) melalui endpoint internal `/api/wilayah/*` (`routes/api.php:16`), implementasi di `app/Http/Controllers/Api/WilayahDbController.php`, konfigurasi `config/services.php`.
- Payment Gateway Midtrans: Snap token (`routes/web.php:44`, `PembayaranController@midtransSnap`) dan webhook notifikasi (`routes/api.php:33`, `PembayaranController@midtransNotification`).
- Login Google: Socialite (`routes/auth.php:24`).

9) Teknologi yang Digunakan
- Framework: Laravel 10 (`composer.json`).
- PHP: 8.1.
- Database: PostgreSQL (`.env` `DB_CONNECTION=pgsql`).
- Auth: Breeze, Sanctum, Socialite (Google).
- Payment: Midtrans (`config/midtrans.php`).
- Frontend: Blade, Tailwind CSS, Vite.
- Migrasi: lihat daftar pada folder `database/migrations` (contoh file disebut pada poin 7).

Integrasi utama mencakup:

* **Google Login (OAuth2 Socialite)**
* **RajaOngkir API (ongkos kirim otomatis)**
* **Midtrans (Payment Gateway – planned)**

---

# 📑 **Daftar Isi**

1. [Fitur Utama](#-fitur-utama)
2. [Tujuan & Manfaat](#-tujuan--manfaat-proyek)
3. [Teknologi](#-teknologi-yang-digunakan)
4. [Role Pengguna](#-struktur-role-pengguna)
5. [Arsitektur Sistem](#-arsitektur-sistem)
6. [Penjelasan Fitur](#-deskripsi-fitur-secara-detail)
7. [Alur Sistem](#-alur-utama-sistem)
8. [Data Master](#-data-master)
9. [Instalasi](#-cara-instalasi-local-setup)
10. [API Integrasi](#-konfigurasi-tambahan-api-rajaongkir--midtrans)
11. [Akses Awal](#-akses-awal-sistem)
12. [Halaman Error](#-halaman-error)
13. [Lisensi](#-license)

---

# 🚀 **Fitur Utama**

## 👤 Untuk Pengunjung & User

* 🐠 Melihat katalog produk
* 🖼️ Detail produk + galeri foto
* 🧺 Menambahkan ke keranjang
* 💳 Checkout
* 🚚 Ongkos kirim otomatis *(RajaOngkir)*
* 💵 Pembayaran COD / Midtrans
* ⭐ Ulasan produk
* 🧾 Riwayat pesanan

## 🛠️ Untuk Admin

* 📊 Dashboard Admin
* 📦 CRUD Produk
* 🏷️ CRUD Kategori & Jenis Ikan
* 🖼️ Upload galeri foto
* 👥 Manajemen User
* 🔐 Role & Permission
* 📑 Manajemen Pesanan & Status
* 📰 Artikel / Konten

## 🚚 Untuk Kurir

* 🛵 Dashboard kurir
* 📦 Melihat pesanan untuk dikirim

## 🔐 Keamanan & Akses

* ✉️ Verifikasi email
* 🔑 Login Google (OAuth2 Socialite)
* 🛡️ Rate limiting (anti brute force)
* 🔒 Session regeneration
* 🧭 Role-based Access (Admin, User, Kurir)

---

# 🎯 **Tujuan & Manfaat Proyek**

## 🎯 Tujuan

* Membangun platform e-commerce perikanan berbasis web
* Mempermudah proses jual-beli & manajemen data
* Menyediakan ongkir otomatis menggunakan API
* Mengintegrasikan sistem pembayaran digital

## 👍 Manfaat

* Pembeli dapat memesan ikan segar dari rumah
* Admin mudah mengelola produk & pesanan
* Meningkatkan pemahaman fullstack Laravel (Auth, API, DB, UI)
* Sistem transaksi yang lebih rapi & aman

---

# ⚠️ **Masalah yang Diselesaikan**

* Digitalisasi transaksi ikan segar
* Menghindari transaksi manual
* Menyediakan ongkir otomatis akurat
* Mempermudah admin mengelola data
* Login lebih cepat menggunakan Google SSO

---

# 🧩 **Teknologi yang Digunakan**

| Layer          | Teknologi                                       |
| -------------- | ----------------------------------------------- |
| Backend        | Laravel 10, PHP 8.1                             |
| Frontend       | Blade, Tailwind CSS, DaisyUI, Remix Icon        |
| Build Tools    | Vite + Node.js                                  |
| Database       | PostgreSQL                                      |
| Authentication | Laravel Breeze, **Socialite (Google)**, Sanctum |
| Payment        | Midtrans Snap (Planned)                         |
| Shipping       | **RajaOngkir API**                              |
| Storage        | Laravel Public Storage                          |

---

# 👥 **Struktur Role Pengguna**

## 🕶️ Guest

* Melihat katalog
* Melihat detail produk

## 🧑 User

* Semua kemampuan guest
* Keranjang belanja
* Checkout
* Hitung ongkir otomatis
* Ulasan produk
* Riwayat pesanan
* Edit profil & alamat

## 🛠️ Admin

* Full access ke dashboard
* CRUD produk / kategori / jenis ikan
* Manajemen user & role
* Manajemen pesanan
* Artikel dan konten

## 🚚 Kurir

* Dashboard kurir
* Melihat pesanan yang harus dikirim

---

# 🧱 **Arsitektur Sistem**

```
Frontend   → Blade, Tailwind, DaisyUI
Backend    → Laravel 10 (MVC)
Database   → PostgreSQL
Auth       → Breeze + Socialite + Sanctum
Shipping   → RajaOngkir API
Payment    → Midtrans (Snap / planned)
Storage    → Public Storage (symlink)
```

---

# 📰 **Deskripsi Fitur Secara Detail**

## 🛒 1. Katalog Produk

* Filter kategori & jenis ikan
* Galeri foto per produk
* Slug URL otomatis

## 🧺 2. Keranjang

* Disimpan via session
* Update jumlah / hapus item

## 🧾 3. Checkout

* Memilih alamat
* Memilih kurir
* Perhitungan ongkir otomatis
* Total = subtotal + ongkir

## 🚚 4. Ongkir (RajaOngkir API)

* Dropdown provinsi → kota → kecamatan
* Request API:

  ```
  origin, destination, weight, courier
  ```
* Mendapatkan:

  * Biaya
  * Estimasi hari (ETD)

## 💵 5. Pembayaran

### COD

* Langsung memproses pesanan

### Midtrans *(planned)*

* Snap Token
* Redirect
* Webhook status

## ⭐ 6. Ulasan Produk

* Hanya untuk user yang sudah membeli

## 🛠️ 7. Panel Admin

* CRUD seluruh master data
* Manajemen pesanan
* Manajemen role & permission

---

# 🔄 **Alur Utama Sistem**

## 👤 Guest → User

```
Lihat Produk → Register/Login → Verifikasi Email → Mulai Belanja
```

## 🧑 User

```
Tambah ke Keranjang → Checkout → Hitung Ongkir
→ Pilih Pembayaran → Pesanan Berhasil
```

## 🛠️ Admin

```
Dashboard → Kelola Produk → Kelola Pesanan → Kelola User
```

## 🚚 Kurir

```
Dashboard → Lihat Pesanan → Antar Pesanan
```

---

# 📦 **Data Master**

## 📁 Katalog Produk

* Produk
* Kategori
* Jenis Ikan
* Galeri Foto Produk

## 👤 Pengguna

* User
* Role & Permission
* Profil pengguna

## 📍 Alamat

* Alamat pengguna
* Data wilayah (provinsi/kota/kecamatan)

## 🧾 Transaksi

* Pesanan
* Item Pesanan
* Pembayaran
* Pengiriman
* Status Pesanan

## ⭐ Konten

* Artikel
* Ulasan Produk

## ⚙️ Pendukung

* Keranjang (session)
* Log Aktivitas

---

# 🔥 **Cara Instalasi (Local Setup)**

### 🧩 Prasyarat

* PHP 8.1+
* Composer
* Node.js 18+
* PostgreSQL
* Git

---

### 1️⃣ Clone Repo

```bash
git clone <repo-url>
cd fishygo
```

### 2️⃣ Setup Environment

```bash
cp .env.example .env
```

Atur:

```env
DB_DATABASE=fishygo
DB_USERNAME=postgres
DB_PASSWORD=password

RAJAONGKIR_API_KEY=xxxx
RAJAONGKIR_ACCOUNT_TYPE=starter
```

### 3️⃣ Install Backend

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 4️⃣ Install Frontend

```bash
npm install
npm run dev
```

### 5️⃣ Jalankan Aplikasi

```bash
php artisan serve        # Backend
npm run dev              # Frontend
```

---

# 🔑 **Akses Awal Sistem**

* Register via `/register`
* Wajib verifikasi email
* Login Google tersedia
* Role default → **User**
* Admin bisa di-set manual via database

---

# ⚠️ **Halaman Error**

* ❌ 403 Forbidden
* ❌ 404 Not Found
* ❌ 419 CSRF Expired
* ❌ 500 Server Error
* ❌ 503 Maintenance Mode

---

# 📬 **Notifikasi**

* Toast (success / error)
* Email verifikasi
* Reset password
* Opsional: Notifikasi pesanan

---

# 📄 **License**

Proyek ini dibuat untuk **Project Akhir Pemrograman Web Lanjut 2**
dan dapat dikembangkan kembali sesuai kebutuhan.

---

