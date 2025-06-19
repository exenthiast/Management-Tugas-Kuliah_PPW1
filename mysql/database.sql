-- Tabel User
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_kampus VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Mata Kuliah
CREATE TABLE IF NOT EXISTS mata_kuliah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_mk VARCHAR(100) NOT NULL,
    jenis ENUM('praktikum', 'teori') NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Tugas
CREATE TABLE IF NOT EXISTS tugas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_tugas VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    id_mk INT NOT NULL,
    deadline DATETIME NOT NULL,
    status ENUM('selesai', 'belum') NOT NULL DEFAULT 'belum',
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mk) REFERENCES mata_kuliah(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Jadwal
CREATE TABLE IF NOT EXISTS jadwal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hari ENUM('senin', 'selasa', 'rabu', 'kamis', 'jumat') NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    id_mk INT NOT NULL,
    id_user INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mk) REFERENCES mata_kuliah(id) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Menambahkan data ke tabel users
INSERT INTO users (nama, email, password, nama_kampus, no_telepon) VALUES
('Budi Santoso', 'budi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Universitas Contoh', '081234567890'),
('Ani Wijaya', 'ani@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Universitas Teknologi', '081234567891'),
('Cindy Permata', 'cindy@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Universitas Informatika', '081234567892');

-- Menambahkan data ke tabel mata_kuliah
INSERT INTO mata_kuliah (nama_mk, jenis, id_user) VALUES
('Pemrograman Web 1', 'praktikum', 4),
('Basis Data', 'praktikum', 4),
('Algoritma dan Struktur Data', 'teori', 4),
('Statistika', 'teori', 4),
('Struktur Data', 'praktikum', 4),
('Aljabar Vektor Matriks', 'teori', 4),
('Bahasa Inggris 2', 'teori', 4),
('Pemrogramanm Berbasis Objek', 'praktikum', 4),
('Pemrogramanm Berbasis Objek', 'teori', 4)

-- Menambahkan data ke tabel tugas
INSERT INTO tugas (nama_tugas, deskripsi, id_mk, deadline, status, id_user) VALUES
('Membuat Website Portfolio', 'Buatlah website portfolio pribadi menggunakan HTML, CSS, dan JavaScript', 1, '2025-06-01 23:59:59', 'belum', 1),
('Perancangan Database Toko Online', 'Membuat ERD dan implementasi database untuk sistem toko online', 2, '2025-05-25 23:59:59', 'selesai', 1),
('Analisis Kompleksitas Algoritma', 'Menganalisis kompleksitas waktu dan ruang dari algoritma sorting', 3, '2025-05-30 23:59:59', 'belum', 1),
('Konfigurasi Router', 'Melakukan konfigurasi router untuk jaringan lokal', 4, '2025-06-10 23:59:59', 'belum', 1),
('Implementasi Algoritma Genetika', 'Implementasikan algoritma genetika untuk masalah optimasi', 5, '2025-05-28 23:59:59', 'belum', 2),
('Instalasi Linux', 'Menginstal dan mengkonfigurasi sistem operasi Linux', 6, '2025-06-05 23:59:59', 'selesai', 2),
('Pembuktian Teorema', 'Buktikan teorema-teorema yang diberikan dalam materi perkuliahan', 7, '2025-06-15 23:59:59', 'belum', 3),
('Aplikasi Android Sederhana', 'Membuat aplikasi Android sederhana dengan fitur CRUD', 8, '2025-05-27 23:59:59', 'selesai', 3);

-- Menambahkan data ke tabel jadwal
INSERT INTO jadwal (hari, jam_mulai, jam_selesai, id_mk, id_user) VALUES
('senin', '08:00:00', '10:30:00', 5, 6),
('senin', '12:30:00', '15:55:00', 2, 6),
('selasa', '12:15:00', '13:55:00', 9, 6),
('selasa', '14:15:00', '15:55:00', 3, 6),
('rabu', '12:15:00', '13:55:00', 4, 6),
('kamis', '07:15:00', '10:55:00', 1, 6),
('kamis', '12:15:00', '13:55:00', 8, 6),
('jumat', '07:15:00', '08:55:00', 7, 6),
('jumat', '09:15:00', '10:55:00', 6, 6);
('jumat', '13:00:00', '14:40:00', 10, 6);

desc users;