-- Database Exam CBT
-- MySQL 8.0+

CREATE DATABASE IF NOT EXISTS exam_cbt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exam_cbt;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'guru', 'siswa') NOT NULL,
    kelas VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Mata Pelajaran
CREATE TABLE IF NOT EXISTS mata_pelajaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_mapel VARCHAR(20) UNIQUE NOT NULL,
    nama_mapel VARCHAR(100) NOT NULL,
    guru_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Bank Soal
CREATE TABLE IF NOT EXISTS bank_soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mapel_id INT NOT NULL,
    guru_id INT NOT NULL,
    jenis_soal ENUM('pilihan_ganda', 'essay') NOT NULL,
    pertanyaan TEXT NOT NULL,
    opsi_a TEXT DEFAULT NULL,
    opsi_b TEXT DEFAULT NULL,
    opsi_c TEXT DEFAULT NULL,
    opsi_d TEXT DEFAULT NULL,
    opsi_e TEXT DEFAULT NULL,
    kunci_jawaban VARCHAR(10) DEFAULT NULL,
    bobot_nilai INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mapel_id) REFERENCES mata_pelajaran(id) ON DELETE CASCADE,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Ujian
CREATE TABLE IF NOT EXISTS ujian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_ujian VARCHAR(50) UNIQUE NOT NULL,
    judul_ujian VARCHAR(200) NOT NULL,
    mapel_id INT NOT NULL,
    guru_id INT NOT NULL,
    durasi_menit INT NOT NULL,
    tanggal_mulai DATETIME NOT NULL,
    tanggal_selesai DATETIME NOT NULL,
    status ENUM('draft', 'aktif', 'selesai') DEFAULT 'draft',
    token_acak VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mapel_id) REFERENCES mata_pelajaran(id) ON DELETE CASCADE,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Ujian Soal (Mapping soal ke ujian)
CREATE TABLE IF NOT EXISTS ujian_soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ujian_id INT NOT NULL,
    soal_id INT NOT NULL,
    urutan INT NOT NULL,
    FOREIGN KEY (ujian_id) REFERENCES ujian(id) ON DELETE CASCADE,
    FOREIGN KEY (soal_id) REFERENCES bank_soal(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Ujian Peserta
CREATE TABLE IF NOT EXISTS ujian_peserta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ujian_id INT NOT NULL,
    siswa_id INT NOT NULL,
    mulai_dikerjakan DATETIME DEFAULT NULL,
    selesai_dikerjakan DATETIME DEFAULT NULL,
    status ENUM('belum', 'sedang', 'selesai') DEFAULT 'belum',
    nilai DECIMAL(5,2) DEFAULT NULL,
    token_dikerjakan VARCHAR(50) DEFAULT NULL,
    FOREIGN KEY (ujian_id) REFERENCES ujian(id) ON DELETE CASCADE,
    FOREIGN KEY (siswa_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Jawaban Peserta
CREATE TABLE IF NOT EXISTS jawaban_peserta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ujian_peserta_id INT NOT NULL,
    soal_id INT NOT NULL,
    jawaban_siswa TEXT DEFAULT NULL,
    benar BOOLEAN DEFAULT NULL,
    FOREIGN KEY (ujian_peserta_id) REFERENCES ujian_peserta(id) ON DELETE CASCADE,
    FOREIGN KEY (soal_id) REFERENCES bank_soal(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Users (password: 'password')
INSERT INTO users (username, password, nama_lengkap, role, kelas) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', NULL),
('guru1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Guru Matematika', 'guru', NULL),
('guru2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Guru Bahasa Indonesia', 'guru', NULL),
('siswa1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Siswa', 'siswa', 'X-A'),
('siswa2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'siswa', 'X-A');
