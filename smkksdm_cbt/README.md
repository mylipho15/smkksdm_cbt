# Aplikasi Exam CBT Berbasis Web

Aplikasi Computer Based Test (CBT) untuk ujian online dengan fitur lengkap untuk Admin, Guru, dan Siswa.

## 📋 Spesifikasi Teknis

- **Environment**: Laragon 6.0 / XAMPP
- **Web Server**: Apache httpd-2.4.54
- **Database**: MySQL 8.0.30
- **Bahasa Pemrograman**: PHP Native
- **Frontend**: HTML5, CSS3, JavaScript

## 🚀 Fitur Aplikasi

### 1. Login Multi-Role
- ✅ Login Admin
- ✅ Login Guru  
- ✅ Login Siswa

### 2. Fitur Admin
- Dashboard statistik
- Manajemen User (Admin, Guru, Siswa)
- Manajemen Mata Pelajaran
- Monitoring semua ujian dan soal

### 3. Fitur Guru
- Dashboard statistik
- Bank Soal (CRUD)
- **Import Soal dari DOCX/TXT** dengan format template
- Soal Pilihan Ganda (A, B, C, D, E)
- Soal Essay
- Manajemen Ujian
- Penilaian Siswa

### 4. Fitur Siswa
- Dashboard ujian
- Daftar ujian tersedia
- Kerjakan ujian online
- Timer ujian
- Lihat nilai

## 📁 Struktur Folder

```
exam-cbt/
├── admin/                  # Panel Admin
│   └── dashboard.php
├── assets/
│   ├── css/
│   ├── js/
│   └── uploads/           # Folder upload file
├── config/
│   ├── config.php         # Konfigurasi aplikasi
│   └── database.sql       # Script database
├── guru/                  # Panel Guru
│   ├── dashboard.php
│   ├── soal.php
│   └── import-soal.php
├── imports/
│   ├── SoalImporter.php   # Parser import soal
│   └── template.txt       # Template format soal
├── models/
│   ├── Auth.php           # Model autentikasi
│   └── Soal.php           # Model soal
├── siswa/                 # Panel Siswa
│   └── dashboard.php
├── views/
│   └── layouts/           # Layout templates
│       ├── main.php
│       ├── admin.php
│       ├── guru.php
│       └── siswa.php
├── login.php              # Halaman login
└── index.php              # Entry point
```

## 🛠️ Instalasi

### 1. Persiapan Environment

Pastikan Anda telah menginstall:
- Laragon 6.0 atau XAMPP
- MySQL 8.0.30

### 2. Setup Database

1. Buka phpMyAdmin (http://localhost/phpmyadmin)
2. Buat database baru bernama `exam_cbt`
3. Import file `config/database.sql`
4. Atau jalankan SQL script di `config/database.sql`

### 3. Konfigurasi Aplikasi

1. Copy folder `exam-cbt` ke dalam folder `www` (Laragon) atau `htdocs` (XAMPP)
2. Buka file `config/config.php`
3. Sesuaikan konfigurasi jika diperlukan:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'exam_cbt');
   define('BASE_URL', 'http://localhost/exam-cbt');
   ```

### 4. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/exam-cbt
```

## 👤 Default Login Credentials

| Role   | Username | Password |
|--------|----------|----------|
| Admin  | admin    | password |
| Guru   | guru1    | password |
| Siswa  | siswa1   | password |

## 📝 Format Import Soal

### Format File TXT/DOCX

```
[JENIS_SOAL:PG]
[MAPEL:Matematika]
[BOBOT:1]
PERTANYAAN: Berapakah hasil dari 5 + 3?
A. 5
B. 6
C. 7
D. 8
E. 9
KUNCI: D

[JENIS_SOAL:ESSAY]
[MAPEL:Bahasa Indonesia]
[BOBOT:2]
PERTANYAAN: Jelaskan pengertian dari puisi bebas!
KUNCI: Puisi bebas adalah puisi yang tidak terikat oleh aturan-aturan seperti rima, irama, dan jumlah baris.
```

### Keterangan Format:
- `JENIS_SOAL`: PG untuk Pilihan Ganda, ESSAY untuk Essay
- `MAPEL`: Nama mata pelajaran
- `BOBOT`: Nilai/bobot soal (default: 1)
- Untuk PG, wajib ada opsi A sampai E
- `KUNCI`: Jawaban benar (A/B/C/D/E untuk PG)
- Pisahkan setiap soal dengan garis kosong

Template lengkap tersedia di: `imports/template.txt`

## 🎯 Jenis Soal yang Didukung

### 1. Pilihan Ganda (A, B, C, D, E)
- Sistem penilaian otomatis
- Kunci jawaban tunggal

### 2. Essay
- Penilaian manual oleh guru
- Bobot nilai dapat disesuaikan

## 🔒 Keamanan

- Password di-hash menggunakan bcrypt
- Session management
- SQL Injection protection (PDO Prepared Statements)
- XSS Protection (htmlspecialchars)
- File upload validation

## 📄 License

Aplikasi ini dibuat untuk keperluan pembelajaran dan dapat dikembangkan lebih lanjut sesuai kebutuhan.

---

**Dibuat dengan ❤️ untuk Pendidikan Indonesia**
