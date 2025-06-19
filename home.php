<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/config.php';
include_once __DIR__ . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/');
}

// Total Tugas
$result_total = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tugas WHERE id_user = '$user_id'");
if (!$result_total) die("Query total error: " . mysqli_error($conn));
$total = mysqli_fetch_assoc($result_total)['total'];

// Tugas Selesai
$result_selesai = mysqli_query($conn, "SELECT COUNT(*) AS selesai FROM tugas WHERE status = 'selesai' AND id_user = '$user_id'");
if (!$result_selesai) die("Query selesai error: " . mysqli_error($conn));
$selesai = mysqli_fetch_assoc($result_selesai)['selesai'];

// Tugas Belum Selesai
$result_belum = mysqli_query($conn, "SELECT COUNT(*) AS belum FROM tugas WHERE status = 'belum' AND id_user = '$user_id'");
if (!$result_belum) die("Query belum selesai error: " . mysqli_error($conn));
$belum = mysqli_fetch_assoc($result_belum)['belum'];

// Tugas Mendekati Deadline
$result_dekat = mysqli_query($conn, "SELECT COUNT(*) AS mendekati FROM tugas WHERE status = 'belum' AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY) AND id_user = '$user_id'");
if (!$result_dekat) die("Query mendekati deadline error: " . mysqli_error($conn));
$mendekati = mysqli_fetch_assoc($result_dekat)['mendekati'];

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css">
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
            <div class="content-body">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <p class="mb-0 text-muted">Selamat datang kembali, <?= htmlspecialchars($nama_lengkap) ?>! Kelola tugas kuliah Anda dengan mudah😘.</p>
                    </div>
                    <a href="CRUD/tambah-tugas.php" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm me-2"></i> Tambah Tugas
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tugas</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tugas Selesai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $selesai ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tugas Belum Selesai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $belum ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Mendekati Deadline</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $mendekati ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deadline Terdekat & Tugas Terbaru -->
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-clock me-2"></i>Deadline Terdekat
                                </h6>
                                <a href="daftar-tugas.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <?php 
                                $result_deadline = mysqli_query($conn, "
                                    SELECT tugas.*, mata_kuliah.nama_mk 
                                    FROM tugas 
                                    JOIN mata_kuliah ON tugas.id_mk = mata_kuliah.id 
                                    WHERE tugas.id_user = '$user_id'
                                    AND tugas.status = 'belum' 
                                    ORDER BY tugas.deadline ASC 
                                    LIMIT 5
                                ");

                                if ($result_deadline && mysqli_num_rows($result_deadline) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_deadline)) : ?>
                                        <div class="task-item pending shadow-sm p-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><?= htmlspecialchars($row['nama_tugas']) ?></h6>
                                                <span class="badge badge-deadline"><?= date('d M Y', strtotime($row['deadline'])) ?></span>
                                            </div>
                                            <div class="small text-muted mt-1"><?= htmlspecialchars($row['nama_mk']) ?></div>
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark"><?= ucfirst(htmlspecialchars($row['status'])) ?></span>
                                            </div>
                                        </div>
                                    <?php endwhile;
                                } else { ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-check fa-3x mb-3 d-block"></i>
                                        <h6>Tidak ada tugas mendekati deadline</h6>
                                        <p class="mb-0">Anda sedang bebas deadline! 🎉</p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Tugas Terbaru
                                </h6>
                                <a href="daftar-tugas.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <?php 
                                $result_terbaru = mysqli_query($conn, "
                                    SELECT tugas.*, mata_kuliah.nama_mk 
                                    FROM tugas 
                                    JOIN mata_kuliah ON tugas.id_mk = mata_kuliah.id 
                                    WHERE tugas.id_user = '$user_id'
                                    ORDER BY tugas.created_at DESC 
                                    LIMIT 5
                                ");

                                if ($result_terbaru && mysqli_num_rows($result_terbaru) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_terbaru)) : ?>
                                        <div class="task-item <?= ($row['status'] == 'selesai') ? 'completed' : 'pending' ?> shadow-sm p-3 mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><?= htmlspecialchars($row['nama_tugas']) ?></h6>
                                                <span class="badge badge-deadline"><?= date('d M Y', strtotime($row['deadline'])) ?></span>
                                            </div>
                                            <div class="small text-muted mt-1"><?= htmlspecialchars($row['nama_mk']) ?></div>
                                            <div class="mt-2">
                                                <span class="badge <?= ($row['status'] == 'selesai') ? 'bg-success text-white' : 'bg-warning text-dark' ?>">
                                                    <?= htmlspecialchars($row['status']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endwhile;
                                } else { ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-tasks fa-3x mb-3 d-block"></i>
                                        <h6>Belum ada tugas</h6>
                                        <p class="mb-0">Mulai tambahkan tugas pertama Anda!</p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <a href="CRUD/tambah-matkul.php" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus fa-sm me-2"></i> Tambah Mata Kuliah
                    </a>
                </div>

                <!-- Jadwal Kuliah Hari Ini -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-calendar-day me-2"></i>
                                    Jadwal Kuliah Hari Ini - <?= date('l, d F Y') ?>
                                </h6>
                                <a href="jadwal-kuliah.php" class="btn btn-sm btn-primary">Lihat Semua</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                        <thead class="table-light">
                                            <tr>
                                                <th><i class="fas fa-clock me-2"></i>Jam</th>
                                                <th><i class="fas fa-book me-2"></i>Mata Kuliah</th>
                                                <th><i class="fas fa-tag me-2"></i>Jenis</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $hari_ini = date('l');
                                            $hari_indonesia = [
                                                'Monday' => 'Senin',
                                                'Tuesday' => 'Selasa',
                                                'Wednesday' => 'Rabu',
                                                'Thursday' => 'Kamis',
                                                'Friday' => 'Jumat',
                                                'Saturday' => 'Sabtu',
                                                'Sunday' => 'Minggu'
                                            ];
                                            $hari_sekarang = $hari_indonesia[$hari_ini];
                                            $sql_jadwal_hari_ini = "SELECT
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
                                                j.hari = '$hari_sekarang'
                                            AND
                                                j.id_user = '$user_id'
                                            ORDER BY
                                                j.jam_mulai ASC";
                                            $result_jadwal = mysqli_query($conn, $sql_jadwal_hari_ini);
                                            if ($result_jadwal && mysqli_num_rows($result_jadwal) > 0) {
                                                while ($row = mysqli_fetch_assoc($result_jadwal)) {
                                                    $jam_mulai = date('H:i', strtotime($row['jam_mulai']));
                                                    $jam_selesai = date('H:i', strtotime($row['jam_selesai']));
                                                    $waktu = $jam_mulai . ' - ' . $jam_selesai;
                                                    $badge_class = '';
                                                    switch (strtolower($row['jenis'])) {
                                                        case 'praktikum':
                                                            $badge_class = 'bg-info text-white';
                                                            break;
                                                        case 'teori':
                                                            $badge_class = 'bg-secondary text-white';
                                                            break;
                                                        case 'seminar':
                                                            $badge_class = 'bg-warning text-dark';
                                                            break;
                                                        default:
                                                            $badge_class = 'bg-primary text-white';
                                                            break;
                                                    }
                                                    echo "<tr>";
                                                    echo "<td><strong>$waktu</strong></td>";
                                                    echo "<td>" . htmlspecialchars($row['nama_mk']) . "</td>";
                                                    echo "<td><span class='badge $badge_class'>" . htmlspecialchars($row['jenis']) . "</span></td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3' class='text-center text-muted py-5'>";
                                                echo "<i class='fas fa-calendar-times fa-3x mb-3 d-block'></i>";
                                                echo "<h6>Tidak ada jadwal kuliah hari ini</h6>";
                                                echo "<p class='mb-0'>Selamat menikmati hari libur! 🎉</p>";
                                                echo "</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const toggleButton = document.getElementById('sidebarCollapse');
            const toggleIcon = toggleButton.querySelector('i');

            if (!sidebar || !mainContent || !toggleButton) {
                console.error('Sidebar, main content, or toggle button not found');
                return;
            }

            toggleButton.addEventListener('click', function (e) {
                e.preventDefault();
                console.log('Toggle button clicked');

                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle('mobile-active');
                    console.log('Mobile mode: mobile-active toggled');
                } else {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');
                    console.log('Desktop mode: collapsed toggled');
                }

                toggleIcon.classList.toggle('fa-bars');
                toggleIcon.classList.toggle('fa-times');
            });

            document.addEventListener('click', function (e) {
                if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-active') &&
                    !e.target.closest('#sidebar') && !e.target.closest('#sidebarCollapse')) {
                    sidebar.classList.remove('mobile-active');
                    toggleIcon.classList.remove('fa-times');
                    toggleIcon.classList.add('fa-bars');
                    console.log('Mobile mode: sidebar closed via outside click');
                }
            });

            sidebar.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                    if (!sidebar.classList.contains('mobile-active')) {
                        sidebar.classList.remove('mobile-active');
                        toggleIcon.classList.remove('fa-times');
                        toggleIcon.classList.add('fa-bars');
                    }
                } else {
                    sidebar.classList.remove('mobile-active');
                    mainContent.style.marginLeft = sidebar.classList.contains('collapsed') ?
                        'var(--collapsed-sidebar-width)' : 'var(--sidebar-width)';
                }
                console.log('Window resized, new width:', window.innerWidth);
            });
        });
    </script>
</body>
</html>