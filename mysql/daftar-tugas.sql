SELECT
    mk.nama_mk AS nama_mk,
    mk.jenis,
    t.deskripsi,
    t.deadline,
    t.status
FROM
    tugas t
JOIN
    mata_kuliah mk ON t.id_mk = mk.id;