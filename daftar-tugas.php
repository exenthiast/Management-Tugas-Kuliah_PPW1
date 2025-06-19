<?php
include_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/config.php';
include_once __DIR__ . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/daftar-tugas.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include_once 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <?php include_once 'components/navbar.php'; ?>

            <!-- Content Body -->
            <div class="container-fluid">
                <div class="schedule-card">
                    <h2 class="text-center mb-4">Daftar Tugas</h2>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Kuliah</th>
                                    <th>Jenis</th>
                                    <th>Deskripsi</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th class="action-column">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT
                                            t.id,
                                            mk.nama_mk AS nama_mk,
                                            mk.jenis,
                                            t.deskripsi,
                                            t.deadline,
                                            t.status
                                        FROM
                                            tugas t
                                        JOIN
                                            mata_kuliah mk ON t.id_mk = mk.id
                                        WHERE
                                            t.id_user = '$user_id'";
                                $result = $conn->query($sql);
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_mk']) . "</td>";
                                        $badge_class = '';
                                        switch(strtolower($row['jenis'])) {
                                            case 'praktikum':
                                                $badge_class = 'badge-praktikum';
                                                break;
                                            case 'seminar':
                                                $badge_class = 'badge-seminar';
                                                break;
                                            default:
                                                $badge_class = 'badge-teori';
                                        }
                                        echo "<td><span class='badge " . $badge_class . "'>" . htmlspecialchars($row['jenis']) . "</span></td>";
                                        echo "<td>" . htmlspecialchars($row['deskripsi']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['deadline']) . "</td>";
                                        
                                        // Add status with color coding
                                        $status = htmlspecialchars($row['status']);
                                        $statusClass = '';
                                        if (strtolower($status) === 'selesai') {
                                            $statusClass = 'status-completed';
                                        } elseif (strtolower($status) === 'belum selesai') {
                                            $statusClass = 'status-pending';
                                        } elseif (strtolower($status) === 'terlambat') {
                                            $statusClass = 'status-overdue';
                                        }
                                        echo "<td class='$statusClass'>" . $status . "</td>";
                                        
                                        // Add action buttons
                                        echo "<td class='action-column'>";
                                        echo "<a href='CRUD/edit-tugas.php?id=" . $row['id'] . "' class='btn-action btn-edit' title='Edit Tugas'>";
                                        echo "<i class='fas fa-edit'></i>";
                                        echo "</a>";
                                        echo "<button type='button' class='btn-action btn-delete' onclick='confirmDelete(" . $row['id'] . ")' title='Hapus Tugas'>";
                                        echo "<i class='fas fa-trash'></i>";
                                        echo "</button>";
                                        echo "</td>";
                                        
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='no-data'>Tidak ada tugas yang ditemukan.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus tugas ini? Data yang telah dihapus tidak dapat dikembalikan.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/daftar-tugas.js"></script>
</body>
</html>
<?php
$conn->close();
?>