INSERT INTO mata_kuliah (nama_mk, jenis, id_user) VALUES
('Pemrograman Web 1', 'praktikum', 6),
('Basis Data', 'praktikum', 6),
('Algoritma dan Struktur Data', 'teori', 6),
('Statistika', 'teori', 6),
('Struktur Data', 'praktikum', 6),
('Aljabar Vektor Matriks', 'teori', 6),
('Bahasa Inggris 2', 'teori', 6),
('Praktikum PBO', 'praktikum', 6),
('Pemrograman Berbasis Objek', 'teori', 6),
('Basis Data', 'teori', 6);

INSERT INTO jadwal (hari, jam_mulai, jam_selesai, id_mk, id_user) VALUES
('senin', '08:00:00', '10:30:00', 5, 6),
('senin', '12:30:00', '15:55:00', 2, 6),
('selasa', '12:15:00', '13:55:00', 9, 6),
('selasa', '14:15:00', '15:55:00', 3, 6),
('rabu', '12:15:00', '13:55:00', 4, 6),
('kamis', '07:15:00', '10:55:00', 1, 6),
('kamis', '12:15:00', '16:55:00', 8, 6),
('jumat', '07:15:00', '08:55:00', 7, 6),
('jumat', '09:15:00', '10:55:00', 6, 6);
('jumat', '13:00:00', '14:40:00', 10, 6);