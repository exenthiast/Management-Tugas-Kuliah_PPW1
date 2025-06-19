<?php
include_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/config.php';
include_once __DIR__ . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/');
}

$mk_query = mysqli_query($conn, "SELECT id, nama_mk FROM mata_kuliah WHERE id_user = '$user_id' ORDER BY nama_mk ASC");
if (!$mk_query) {
    die('Error query mata kuliah: ' . mysqli_error($conn));
}

$success = '';
$errors = [];

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_jadwal = (int)$_GET['delete'];
    
    // Verify ownership before deleting
    $check_query = "SELECT j.id FROM jadwal j 
                   JOIN mata_kuliah mk ON j.id_mk = mk.id 
                   WHERE j.id = $id_jadwal AND mk.id_user = '$user_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $delete_query = "DELETE FROM jadwal WHERE id = $id_jadwal";
        if (mysqli_query($conn, $delete_query)) {
            $success = "Jadwal berhasil dihapus!";
        } else {
            $errors[] = "Gagal menghapus jadwal: " . mysqli_error($conn);
        }
    } else {
        $errors[] = "Jadwal tidak ditemukan atau tidak memiliki akses!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kuliah - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --collapsed-sidebar-width: 70px;
            --navbar-height: 70px;
            --primary-color: #4e73df;
            --primary-dark: #3759c5;
            --bg-color: #f8f9fc;
            --text-primary: #5a5c69;
            --text-secondary: #858796;
            --border-color: #e3e6f0;
            --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --sidebar-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100%;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            transition: width 0.3s ease, transform 0.3s ease;
            z-index: 1001;
            box-shadow: var(--sidebar-shadow);
        }

        #sidebar.collapsed {
            width: var(--collapsed-sidebar-width);
        }

        @media (max-width: 768px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.mobile-active {
                transform: translateX(0);
            }
        }

        #sidebar .sidebar-header {
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            height: var(--navbar-height);
            display: flex;
            align-items: center;
        }

        #sidebar.collapsed .sidebar-header h4 {
            display: none;
        }

        #sidebar.collapsed .sidebar-header {
            padding: 20px 10px;
            justify-content: center;
        }

        #sidebar ul.components {
            padding: 20px 0;
            list-style: none;
            margin: 0;
        }

        #sidebar ul li a {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }

        #sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: white;
        }

        #sidebar ul li a.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-left-color: white;
        }

        #sidebar ul li a i {
            width: 20px;
            text-align: center;
            margin-right: 15px;
            font-size: 16px;
            flex-shrink: 0;
        }

        #sidebar.collapsed ul li a {
            padding: 15px 10px;
            justify-content: center;
        }

        #sidebar.collapsed ul li a span {
            display: none;
        }

        #sidebar.collapsed ul li a i {
            margin-right: 0;
            font-size: 18px;
        }

        .main-content {
            flex: 1;
            width: 100%;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .schedule-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background-color: #4e73df;
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #e3e6f0;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        
        tr:hover {
            background-color: #f1f3f9;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
        
        .badge-teori {
            background-color: #4e73df;
        }
        
        .badge-praktikum {
            background-color: #1cc88a;
        }
        
        .badge-seminar {
            background-color: #f6c23e;
        }

        .main-content.collapsed {
            margin-left: var(--collapsed-sidebar-width);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .main-content.collapsed {
                margin-left: 0;
            }
        }

        .top-navbar {
            position: sticky;
            top: 0;
            width: 100%;
            background: white;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            padding: 0 20px;
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
        }

        .btn-sidebar-toggle {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-sidebar-toggle:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.4);
        }

        .btn-sidebar-toggle:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
        }

        .content-body {
            padding: 20px;
            background-color: var(--bg-color);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.3rem 2rem 0 rgba(58, 59, 69, 0.2);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--primary-color);
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }

        .card-body {
            padding: 20px;
        }

        .card-stats {
            border-left: 4px solid;
            background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
        }

        .card-stats.primary { border-left-color: var(--primary-color); }
        .card-stats.success { border-left-color: #1cc88a; }
        .card-stats.warning { border-left-color: #f6c23e; }
        .card-stats.danger { border-left-color: #e74a3b; }

        .card-stats .card-body {
            padding: 15px 20px;
        }

        .card-stats .text-xs {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-stats .h5 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 8px 0 0 0;
        }

        .card-stats .fa-2x {
            font-size: 2em;
            opacity: 0.3;
        }

        .task-item {
            border-left: 4px solid;
            margin-bottom: 15px;
            border-radius: 8px;
            background: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .task-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .task-item.completed {
            border-left-color: #1cc88a;
            background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
        }

        .task-item.pending {
            border-left-color: #f6c23e;
            background: linear-gradient(135deg, #ffffff 0%, #fffbf0 100%);
        }

        .task-item h6 {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .badge-deadline {
            background-color: var(--bg-color);
            color: var(--text-secondary);
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: #4e73df;
            color: white;
            border: none;
            font-weight: 600;
            padding: 12px;
            text-align: center;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 12px 20px;
            border-color: var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
        }

        .user-dropdown .dropdown-toggle {
            border: none;
            background: none;
            color: var(--text-primary);
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .user-dropdown .dropdown-toggle:hover {
            background-color: rgba(78, 115, 223, 0.1);
        }

        .chart-loading {
            text-align: center;
            padding: 20px;
            color: var(--text-secondary);
        }

        /* Action Buttons Styling */        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 6px;
            font-size: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 30px;
            height: 30px;
            position: relative;
            z-index: 1;
            pointer-events: auto;
            cursor: pointer;
        }
        
        .btn-edit {
            background-color: #ffc107;
            border: 1px solid #ffc107;
            color: #212529 !important;
            text-decoration: none !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-edit:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #212529 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
        }

        .btn-delete {
            background-color: #dc3545;
            border: 1px solid #dc3545;
            color:  white !important;
        }

        .btn-delete:hover {
            background-color: #c82333;
            border-color: #bd2130;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .action-buttons a {
            text-decoration: none !important;
            color: inherit;
        }

        /* Alert Styles */
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        /* Add Jadwal Button */
        .btn-add-jadwal {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add-jadwal:hover {
            background: linear-gradient(135deg, #17a673 0%, #138960 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(23, 162, 115, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include_once ROOT_PATH . 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <!-- Top Navbar -->
            <?php include_once ROOT_PATH . 'components/navbar.php'; ?>

            <!-- Content Body -->
            <div class="container-fluid">
                <!-- Alert Messages -->
                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="schedule-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Jadwal Kuliah</h2>
                        <a href="/CRUD/tambah-jadwal.php" class="btn-add-jadwal">
                            <i class="fas fa-plus"></i>
                            Tambah Jadwal
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Kuliah</th>
                                    <th>Jenis</th>
                                    <th>Hari</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT
                                            j.id AS id_jadwal,
                                            mk.nama_mk AS nama_mk,
                                            mk.jenis,
                                            j.hari,
                                            j.jam_mulai,
                                            j.jam_selesai
                                        FROM
                                            jadwal j
                                        JOIN
                                            mata_kuliah mk ON j.id_mk = mk.id
                                        WHERE
                                            mk.id_user = '$user_id'
                                        ORDER BY 
                                            FIELD(j.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'),
                                            j.jam_mulai";
                                
                                $result = mysqli_query($conn, $sql);
                                
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
                                        
                                        echo "<td>" . htmlspecialchars($row['hari']) . "</td>";
                                        echo "<td>" . date('H:i', strtotime($row['jam_mulai'])) . "</td>";
                                        echo "<td>" . date('H:i', strtotime($row['jam_selesai'])) . "</td>";
                                        
                                        // Action buttons
                                        echo "<td>";
                                        echo "<div class='action-buttons'>";
                                        echo "<a href='/CRUD/edit-jadwal.php?id=" . $row['id_jadwal'] . "' class='btn btn-action btn-edit' title='Edit Jadwal'>";
                                        echo "<i class='fas fa-edit'></i>";
                                        echo "</a>";
                                        echo "<a href='#' class='btn btn-action btn-delete' title='Hapus Jadwal' ";
                                        echo "onclick='confirmDelete(" . $row['id_jadwal'] . ", \"" . htmlspecialchars($row['nama_mk']) . "\", \"" . htmlspecialchars($row['hari']) . "\")'>";
                                        echo "<i class='fas fa-trash'></i>";
                                        echo "</a>";
                                        echo "</div>";
                                        echo "</td>";
                                        
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='no-data'>Tidak ada jadwal kuliah.</td></tr>";
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
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1">Apakah Anda yakin ingin menghapus jadwal kuliah berikut?</p>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Mata Kuliah:</strong> <span id="deleteMataKuliah"></span><br>
                        <strong>Hari:</strong> <span id="deleteHari"></span>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Data yang sudah dihapus tidak dapat dikembalikan!</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and other scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/jadwal-kuliah.js"></script>
    <script>
        function confirmDelete(id, mataKuliah, hari) {
            document.getElementById('deleteMataKuliah').textContent = mataKuliah;
            document.getElementById('deleteHari').textContent = hari;
            document.getElementById('confirmDeleteBtn').href = '/jadwal-kuliah.php?delete=' + id;
            
            // Show the modal
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Handle edit buttons
            document.querySelectorAll('.btn-edit').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    console.log('Redirecting to:', url);
                    window.location.href = url;
                }, true);

                // Log the button and its attributes
                console.log('Found edit button:', button);
                console.log('href attribute:', button.getAttribute('href'));
            });

            // Alternative handling using event delegation
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-edit')) {
                    e.preventDefault();
                    const button = e.target.closest('.btn-edit');
                    const url = button.getAttribute('href');
                    console.log('Delegated click - redirecting to:', url);
                    window.location.href = url;
                }
            }, true);
        });
    </script>
</body>
</html>