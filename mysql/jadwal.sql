SELECT
    mk.nama_mk AS nama_mk,
    mk.jenis,
    j.hari,
    j.jam_mulai,
    j.jam_selesai
FROM
    jadwal j
JOIN
    mata_kuliah mk ON j.id_mk = mk.id
ORDER BY 
    FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'),
    j.jam_mulai;