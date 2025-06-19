<?php
include_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/config/config.php';
include_once __DIR__ . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/');
}

$bulan = date('n');
$tahun = date('Y');
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$hari_pertama = date('w', strtotime("$tahun-$bulan-01"));
$nama_hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalender <?= $tahun ?> - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/kalender.css">
    <style>
        .main-content {
            flex: 1;
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .main-content.collapsed {
            width: calc(100% - var(--collapsed-sidebar-width));
            margin-left: var(--collapsed-sidebar-width);
        }

        @media (max-width: 768px) {
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            .main-content.collapsed {
                width: 100%;
                margin-left: 0;
            }
        }

        .calendar-container {
            transition: all 0.3s ease;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .table-responsive {
            transition: all 0.3s ease;
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
            <div class="content-body">
                <div class="calendar-container">
                    <h2 class="text-center mb-4 text-primary">Kalender <?= date('F Y') ?></h2>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <?php foreach ($nama_hari as $hari): ?>
                                        <th class="text-center"><?= $hari ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    for ($i = 0; $i < $hari_pertama; $i++) {
                                        echo '<td></td>';
                                    }
                                    for ($hari = 1; $hari <= $jumlah_hari; $hari++) {
                                        $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $hari);
                                        $kelas = '';
                                        $keterangan = '';
                                        if ($tanggal == date('Y-m-d')) {
                                            $kelas = 'today';
                                        }
                                        if (isset($libur_nasional) && array_key_exists($tanggal, $libur_nasional)) {
                                            $kelas = 'libur';
                                            $keterangan = '<br><small>' . $libur_nasional[$tanggal] . '</small>';
                                        }
                                        echo "<td class='text-center $kelas'>$hari$keterangan</td>";
                                        if (date('w', strtotime($tanggal)) == 6 && $hari != $jumlah_hari) {
                                            echo '</tr><tr>';
                                        }
                                    }
                                    ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            function updateLayout() {
                const isMobile = $(window).width() <= 768;
                const isCollapsed = $('#sidebar').hasClass('collapsed');
                
                if (isMobile) {
                    $('.main-content').css({
                        'width': '100%',
                        'margin-left': '0'
                    });
                } else {
                    $('.main-content').css({
                        'width': isCollapsed ? `calc(100% - var(--collapsed-sidebar-width))` : `calc(100% - var(--sidebar-width))`,
                        'margin-left': isCollapsed ? 'var(--collapsed-sidebar-width)' : 'var(--sidebar-width)'
                    });
                }
            }

            // Toggle sidebar
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('collapsed');
                $('.main-content').toggleClass('collapsed');
                
                if ($(window).width() <= 768) {
                    $('#sidebar').toggleClass('mobile-active');
                }
                
                updateLayout();
            });

            // Handle responsive behavior
            function handleResize() {
                const width = $(window).width();
                if (width <= 768) {
                    $('#sidebar').removeClass('collapsed').addClass('mobile-active');
                    $('.main-content').removeClass('collapsed');
                } else {
                    $('#sidebar').removeClass('mobile-active');
                }
                updateLayout();
            }

            // Close sidebar on outside click for mobile
            $(document).on('click', function (e) {
                if ($(window).width() <= 768 && $('#sidebar').hasClass('mobile-active') && !$(e.target).closest('#sidebar, #sidebarCollapse').length) {
                    $('#sidebar').removeClass('mobile-active');
                }
            });

            // Prevent sidebar from closing when clicking inside
            $('#sidebar').on('click', function (e) {
                e.stopPropagation();
            });

            // Initialize and handle resize
            handleResize();
            $(window).on('resize', handleResize);
            
            // Initial call to set correct layout
            handleResize();
            updateLayout();
        });
    </script>
</body>
</html>