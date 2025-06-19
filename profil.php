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
    <title>Profil - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/profil.css">
</head>
<body>
    <div class="app-container">
        <?php include_once ROOT_PATH . 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <?php include_once ROOT_PATH . 'components/navbar.php'; ?>

            <div class="content-body">
                <div class="container-fluid">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Profil</a></li>
                            <li class="breadcrumb-item"><a href="CRUD/edit-profil.php">Edit Profil</a></li>
                        </ol>
                    </nav>
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="profile-card">
                                <!-- Profile Header -->
                                <div class="profile-header">
                                    <div class="profile-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h3 class="mb-2"><?= htmlspecialchars($users['nama']) ?></h3>
                                    <p class="text-muted mb-3"><?= htmlspecialchars($users['nama_kampus']) ?></p>
                                    <a href="CRUD/edit-profil.php" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Edit Profil
                                    </a>
                                </div>

                                <!-- Profile Information -->
                                <div class="profile-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="profile-info-item">
                                                <div class="profile-info-label">
                                                    <i class="fas fa-user me-2"></i>Nama Lengkap
                                                </div>
                                                <div class="profile-info-value">
                                                    <?= htmlspecialchars($users['nama']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="profile-info-item">
                                                <div class="profile-info-label">
                                                    <i class="fas fa-envelope me-2"></i>Email
                                                </div>
                                                <div class="profile-info-value">
                                                    <?= htmlspecialchars($users['email']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="profile-info-item">
                                                <div class="profile-info-label">
                                                    <i class="fas fa-university me-2"></i>Nama Kampus
                                                </div>
                                                <div class="profile-info-value">
                                                    <?= htmlspecialchars($users['nama_kampus']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="profile-info-item">
                                                <div class="profile-info-label">
                                                    <i class="fas fa-phone me-2"></i>Nomor Telepon
                                                </div>
                                                <div class="profile-info-value">
                                                    <?= htmlspecialchars($users['no_telepon']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
<?php
mysqli_close($conn);
?>