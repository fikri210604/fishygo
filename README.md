
# 🐟 **E-Commerce Ikan Segar**

**Project Akhir Pemrograman Web Lanjut 2 – Laravel 10**

Aplikasi E-Commerce Ikan Segar merupakan sebuah platform penjualan produk perikanan secara online yang dikembangkan menggunakan Laravel 10 sebagai framework utama. Sistem ini dirancang untuk mendigitalisasi proses jual-beli ikan segar, mulai dari tahap penelusuran produk hingga tahap transaksi dan pengiriman, sehingga memberikan pengalaman belanja yang lebih cepat, mudah, dan terstruktur bagi pengguna.

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

