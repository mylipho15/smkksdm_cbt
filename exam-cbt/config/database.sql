-- Database: exam_cbt
-- Host: localhost
-- User: root

CREATE DATABASE IF NOT EXISTS exam_cbt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE exam_cbt;

-- Tabel Users (untuk Admin, Guru, Siswa)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(200) NOT NULL,
    role ENUM('admin', 'guru', 'siswa') NOT NULL,
    kelas VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Mata Pelajaran
CREATE TABLE mata_pelajaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_mapel VARCHAR(50) UNIQUE NOT NULL,
    nama_mapel VARCHAR(200) NOT NULL,
    guru_id INT,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel Bank Soal
CREATE TABLE bank_soal (
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
);

-- Tabel Ujian
CREATE TABLE ujian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_ujian VARCHAR(50) UNIQUE NOT NULL,
    judul_ujian VARCHAR(200) NOT NULL,
    mapel_id INT NOT NULL,
    guru_id INT NOT NULL,
    tanggal_mulai DATETIME NOT NULL,
    tanggal_selesai DATETIME NOT NULL,
    durasi_menit INT NOT NULL,
    jumlah_soal INT NOT NULL,
    status ENUM('draft', 'aktif', 'selesai') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mapel_id) REFERENCES mata_pelajaran(id) ON DELETE CASCADE,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Detail Ujian (Soal yang akan muncul dalam ujian)
CREATE TABLE ujian_soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ujian_id INT NOT NULL,
    bank_soal_id INT NOT NULL,
    urutan INT NOT NULL,
    FOREIGN KEY (ujian_id) REFERENCES ujian(id) ON DELETE CASCADE,
    FOREIGN KEY (bank_soal_id) REFERENCES bank_soal(id) ON DELETE CASCADE
);

-- Tabel Peserta Ujian
CREATE TABLE ujian_peserta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ujian_id INT NOT NULL,
    siswa_id INT NOT NULL,
    token_ujian VARCHAR(50) UNIQUE,
    mulai_mengerjakan DATETIME DEFAULT NULL,
    selesai_mengerjakan DATETIME DEFAULT NULL,
    status ENUM('belum', 'sedang', 'selesai') DEFAULT 'belum',
    nilai DECIMAL(5,2) DEFAULT NULL,
    FOREIGN KEY (ujian_id) REFERENCES ujian(id) ON DELETE CASCADE,
    FOREIGN KEY (siswa_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Jawaban Siswa
CREATE TABLE jawaban_siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ujian_peserta_id INT NOT NULL,
    soal_id INT NOT NULL,
    jawaban_siswa TEXT,
    is_correct TINYINT(1) DEFAULT 0,
    FOREIGN KEY (ujian_peserta_id) REFERENCES ujian_peserta(id) ON DELETE CASCADE,
    FOREIGN KEY (soal_id) REFERENCES bank_soal(id) ON DELETE CASCADE
);

-- Insert Data Dummy Admin
INSERT INTO users (username, password, nama_lengkap, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert Data Dummy Guru
INSERT INTO users (username, password, nama_lengkap, role) VALUES 
('guru1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Guru Matematika', 'guru'),
('guru2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Guru Bahasa Indonesia', 'guru');

-- Insert Data Dummy Siswa
INSERT INTO users (username, password, nama_lengkap, role, kelas) VALUES 
('siswa1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siswa Kelas X-A', 'siswa', 'X-A'),
('siswa2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siswa Kelas X-B', 'siswa', 'X-B');

-- Insert Data Dummy Mata Pelajaran
INSERT INTO mata_pelajaran (kode_mapel, nama_mapel, guru_id) VALUES 
('MTK-001', 'Matematika', 2),
('BIN-001', 'Bahasa Indonesia', 3);
