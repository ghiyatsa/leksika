<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/badge/Language-PHP_8.2-777BB4?style=flat&logo=php&logoColor=white">
  <img alt="PHP" src="https://img.shields.io/badge/Language-PHP_8.2-777BB4?style=flat&logo=php&logoColor=white">
</picture>
![CodeIgniter 4](https://img.shields.io/badge/Framework-CodeIgniter_4.7-DD4814?style=flat&logo=codeigniter&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL_8-4479A1?style=flat&logo=mysql&logoColor=white)
![Firebase](https://img.shields.io/badge/Auth-Firebase-FFCA28?style=flat&logo=firebase&logoColor=black)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

<br>
<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://img.shields.io/badge/Leksika-7C5CFC?style=for-the-badge&logo=graduation-cap&logoColor=white">
  <img alt="Leksika" src="https://img.shields.io/badge/Leksika-7C5CFC?style=for-the-badge&logo=graduation-cap&logoColor=white">
</picture>

# Leksika — Pengecekan Orisinalitas Judul Skripsi

**Leksika** adalah aplikasi berbasis web untuk mendeteksi tingkat kemiripan antar judul skripsi menggunakan algoritma _hybrid TF-IDF Cosine Similarity_ dan _Jaccard Similarity_. Dikembangkan untuk Program Studi Teknik Informatika, Universitas Malikussaleh.

---

## Daftar Isi

- [Fitur](#fitur)
- [Tech Stack](#tech-stack)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Penggunaan](#penggunaan)
- [Struktur Database](#struktur-database)
- [Deployment](#deployment)
- [API](#api)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

---

## Fitur

| Fitur | Deskripsi |
|-------|-----------|
| **Cek Kemiripan** | Masukkan judul dan kata kunci, sistem akan menghitung skor similaritas terhadap seluruh dataset judul skripsi yang tersimpan |
| **Hybrid Similarity** | Menggabungkan **TF-IDF Cosine Similarity** (bobot vektor) dan **Jaccard Similarity** (irisan kata) dengan bobot yang dapat diatur |
| **Riwayat Pengecekan** | Semua hasil pengecekan tersimpan dan dapat dilihat kembali kapan saja |
| **Filter & Kategori Hasil** | Hasil dikategorikan otomatis: **Aman**, **Perlu Ditinjau**, atau **Sangat Mirip** berdasarkan ambang batas yang dikonfigurasi |
| **Manajemen Data** | CRUD untuk judul skripsi, mahasiswa, kategori topik, pengguna, dan pengaturan ambang batas |
| **Autentikasi Firebase** | Login dengan Email/Password atau Google, termasuk fitur reset password dan verifikasi email |
| **Dua Peran Pengguna** | **Admin** (dashboard, manajemen data) dan **User/Mahasiswa** (cek kemiripan, riwayat) |
| **Mode Gelap/Terang** | Toggle tema dengan preferensi tersimpan di localStorage |
| **Responsif** | Antarmuka yang responsif untuk desktop dan perangkat seluler |

## Tech Stack

| Lapisan | Teknologi |
|---------|-----------|
| **Framework** | [CodeIgniter 4.7](https://codeigniter.com) |
| **Bahasa** | PHP 8.2+ |
| **Database** | MySQL 8 / MariaDB |
| **Autentikasi** | Firebase Authentication (Email/Password + Google) |
| **Firebase Admin** | Firebase Admin SDK via REST API |
| **Text Processing** | Sastrawi (stemming & stopword removal Bahasa Indonesia) |
| **JWT** | firebase/php-jwt |
| **Server** | FrankenPHP / Apache / Nginx + PHP-FPM |
| **CI/CD** | GitHub Actions |
| **Frontend** | CSS murni (tanpa framework), Chart.js, Firebase JS SDK |

## Persyaratan Sistem

- PHP 8.2 atau lebih baru
- Ekstensi PHP: `intl`, `mbstring`, `json`, `mysqlnd`, `curl`, `fileinfo`
- Composer 2.x
- MySQL 8.0+ atau MariaDB 10.5+
- Node.js (untuk minifikasi aset — opsional)
- Server: FrankenPHP (direkomendasikan), Nginx + PHP-FPM, atau Apache

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/ghiyatsa/leksika.git
cd leksika
```

### 2. Install Dependensi

```bash
composer install
```

### 3. Konfigurasi Lingkungan

```bash
cp env .env
```

Edit `.env` sesuai lingkungan Anda:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080'

database.default.hostname = localhost
database.default.database = db_skripsi_similarity
database.default.username = root
database.default.password =

firebase.projectId = 'your-firebase-project-id'
firebase.apiKey = 'your-firebase-api-key'
firebase.authDomain = 'your-project.firebaseapp.com'
firebase.credentialsPath = '/path/to/firebase-credentials.json'
```

### 4. Konfigurasi Firebase

1. Buat proyek di [Firebase Console](https://console.firebase.google.com)
2. Aktifkan metode login **Email/Password** dan **Google**
3. Buat _service account_ dan unduh file JSON kredensial
4. Simpan kredensial di luar web root (contoh: `/home/admin/credentials/firebase-credentials.json`)
5. Set `firebase.credentialsPath` di `.env` sesuai path tersebut

### 5. Database

```bash
php spark migrate
php spark db:seed DatabaseSeeder
```

Perintah di atas akan:
- Membuat semua tabel yang diperlukan
- Mengisi data awal: pengguna, mahasiswa, kategori, judul skripsi, pengaturan threshold
- Membuat akun pengguna di Firebase (jika terkoneksi)

### 6. Jalankan Aplikasi

**FrankenPHP (direkomendasikan)**

```bash
php frankenphp start
```

**PHP Built-in Server (development)**

```bash
php spark serve
```

Akses di `http://localhost:8080`

### 7. Minifikasi Aset (Produksi)

```bash
npx lightningcss-cli --minify public/css/style.css -o public/css/style.min.css
npx terser public/js/app.js -o public/js/app.min.js -c -m
```

## Penggunaan

### Akun Bawaan (Seeder)

| Peran | Email | Password |
|-------|-------|----------|
| Admin | `admin@leksika.com` | `admin123` |
| User | `user@leksika.com` | `user123` |
| Demo User | `demo@leksika.com` | `demo123` |
| Mahasiswa A | `mahasisw1@leksika.com` | `mhs123` |

### Cek Kemiripan

1. Login sebagai **User** atau **Admin**
2. Buka menu **Cek Kemiripan**
3. Masukkan judul skripsi yang ingin diperiksa
4. (Opsional) Tambahkan kata kunci
5. Klik **Cek Sekarang**
6. Sistem akan menampilkan hingga 5 judul paling mirip dengan skor similaritas

### Diagram Alur Pengecekan

```
Input Judul + Keyword
        │
        ▼
┌─────────────────┐
 │  TextPreprocessor │
 │  • Case folding    │
 │  • Cleansing       │
 │  • Stopword removal│
 │  • Stemming        │
 └────────┬──────────┘
          │
          ▼
┌─────────────────┐
 │ SimilarityCalc   │
 │  • TF-IDF Vector  │
 │  • Cosine Sim.    │
 │  • Jaccard Sim.   │
 │  • Hybrid Score   │
 └────────┬──────────┘
          │
          ▼
┌─────────────────┐
 │   Hasil (Top 5)  │
 │  Sangat Mirip    │
 │  Perlu Ditinjau  │
 │  Aman            │
 └─────────────────┘
```

### Interpretasi Skor

| Kategori | Rentang Hybrid | Tindakan |
|----------|---------------|----------|
| 🟢 Aman | 0.00 – 0.39 | Judul aman digunakan |
| 🟡 Perlu Ditinjau | 0.40 – 0.74 | Disarankan merevisi judul |
| 🔴 Sangat Mirip | 0.75 – 1.00 | Judul terlalu mirip, harus diganti |

*Ambang batas dapat disesuaikan oleh Admin di menu **Pengaturan Threshold**.*

## Struktur Database

```
users
├── id (PK)
├── name
├── email (UNIQUE)
├── password
├── role (admin|user)
├── firebase_uid (UNIQUE)
├── avatar
└── google_avatar

topic_categories
├── id (PK)
├── category_name
└── description

students
├── id (PK)
├── student_id (UNIQUE)
└── name

thesis
├── id (PK)
├── student_id (FK → students.id)
├── category_id (FK → topic_categories.id)
├── title
├── keyword
├── abstract
├── year
└── preprocessed_text

similarity_checks
├── id (PK)
├── user_id (FK → users.id)
├── uuid (UNIQUE)
├── input_title
└── checked_at

similarity_check_details
├── id (PK)
├── check_id (FK → similarity_checks.id)
├── thesis_id (FK → thesis.id)
├── cosine_score
├── jaccard_score
├── hybrid_score
└── result_category

threshold_settings
├── id (PK)
├── cosine_weight
├── jaccard_weight
├── similar_threshold
├── review_threshold
└── max_similarity_results
```

### Relasi

```
users 1─N similarity_checks 1─N similarity_check_details N─1 thesis
                                                                    │
students 1─1 thesis N─1 topic_categories                           │
students 1─1 thesis ───────────────────────────────────────────────┘
```

## Deployment

### GitHub Actions (Otomatis)

Repository ini dilengkapi workflow GitHub Actions di `.github/workflows/deploy.yml` yang secara otomatis:

1. Mengecek kode dari branch `master`
2. Menginstal dependensi Composer
3. Menyalin file ke server via rsync
4. Menjalankan migrasi database
5. Meminifikasi aset CSS/JS
6. Me-reboot FrankenPHP

### Manual (VPS)

```bash
# Pull perubahan
git pull origin master

# Install dependensi
composer install --no-dev --optimize-autoloader

# Copy env
cp .env.production .env

# Migrasi database
php spark migrate

# Seed data awal (hanya sekali)
php spark db:seed DatabaseSeeder

# Minifikasi aset
npx lightningcss-cli --minify public/css/style.css -o public/css/style.min.css
npx terser public/js/app.js -o public/js/app.min.js -c -m

# Restart server
sudo systemctl restart frankenphp-ci4
```

### Struktur Direktori Server

```
public_html/
├── app/
├── public/
├── vendor/
├── writable/
├── .env
├── spark
└── Caddyfile
```

## Keamanan

- **Password** disimpan dengan bcrypt (`password_hash`)
- **Session** terenkripsi dan aman
- **CSRF Protection** aktif untuk semua form
- **Firebase Admin SDK** untuk operasi sensitif (create/delete user)
- **Validasi input** di semua endpoint
- **Filter akses** berdasarkan peran (Admin, User)

## Lisensi

Proyek ini dilisensikan di bawah **MIT License** — lihat file [LICENSE](LICENSE) untuk detail.

---

<p align="center">
  Dikembangkan untuk Program Studi Teknik Informatika<br>
  Universitas Malikussaleh
</p>
