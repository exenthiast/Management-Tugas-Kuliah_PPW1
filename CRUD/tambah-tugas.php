<?php
include_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Ambil data mata kuliah untuk dropdown
$mata_kuliah = [];
$mk_query = mysqli_query($conn, "SELECT id, nama_mk FROM mata_kuliah");
if (!$mk_query) {
    die('Error query mata kuliah: ' . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($mk_query)) {
    $mata_kuliah[] = $row;
}

$success = '';
$errors = [];

if (isset($_POST['submit'])) {
    $nama_tugas = $_POST['nama_tugas'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $id_mk = $_POST['id_mk'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $status = $_POST['status'] ?? '';
    $id_user = $user_id; // Menggunakan ID user yang sudah login

    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Validasi
    if (empty($nama_tugas)) $errors[] = "Nama tugas tidak boleh kosong";
    if (empty($deskripsi)) $errors[] = "Deskripsi tidak boleh kosong";
    if (empty($id_mk)) $errors[] = "Mata Kuliah harus dipilih";
    if (empty($deadline)) $errors[] = "Deadline tidak boleh kosong";
    if (empty($status)) $errors[] = "Status tidak boleh kosong";

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO tugas (nama_tugas, deskripsi, id_mk, deadline, status, id_user, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die('Error prepare statement: ' . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "ssssssss", $nama_tugas, $deskripsi, $id_mk, $deadline, $status, $id_user, $created_at, $updated_at);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Tugas berhasil ditambahkan.";
            // Reset form values
            $_POST = array();
        } else {
            $errors[] = "Gagal menambahkan tugas: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas - Management Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        /* Sidebar Styles */
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

        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            border-color: #6c757d;
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            border-color: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
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

        /* Form Styles */
        .form-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            outline: none;
        }

        .alert {
            border-radius: 8px;
            border: none;
            font-weight: 500;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #d1edff;
            color: #0c5460;
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-color);
        }

        .section-header i {
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .section-header h4 {
            color: var(--text-primary);
            margin: 0;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-group-custom {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 576px) {
            .btn-group-custom {
                flex-direction: column;
            }

            .form-card {
                padding: 20px;
            }
        }

        .readonly-field {
            background-color: #f8f9fa;
            border-color: #e9ecef;
        }

        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-card h5 {
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-card p {
            margin: 0;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include_once ROOT_PATH . 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <?php include_once ROOT_PATH . 'components/navbar.php'; ?>

            <div class="content-body">
                <div class="container-fluid">

                    <div class="info-card">
                        <h5><i class="fas fa-calendar-plus me-2"></i>Tambah Tugas Kuliah</h5>
                        <p>Tambahkan tugas kuliah baru dengan mengisi form berikut😍.</p>
                    </div>

                    <!-- Alert Messages -->
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-check-circle me-2"></i>Berhasil!</strong>
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Error!</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <!-- Form Tambah Tugas -->
                            <div class="form-card">
                                <div class="section-header">
                                    <i class="fas fa-plus-circle"></i>
                                    <h4>Tambah Tugas Baru</h4>
                                </div>

                                <form method="post" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nama_tugas" class="form-label">
                                                    <i class="fas fa-tasks me-2"></i>Nama Tugas
                                                </label>
                                                <input type="text" class="form-control" name="nama_tugas" id="nama_tugas" 
                                                       value="<?= isset($_POST['nama_tugas']) ? htmlspecialchars($_POST['nama_tugas']) : '' ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_mk" class="form-label">
                                                    <i class="fas fa-book me-2"></i>Mata Kuliah
                                                </label>
                                                <select class="form-select" name="id_mk" id="id_mk" required>
                                                    <option value="">-- Pilih Mata Kuliah --</option>
                                                    <?php if (!empty($mata_kuliah)): ?>
                                                        <?php foreach ($mata_kuliah as $mk): ?>
                                                            <option value="<?= $mk['id'] ?>" 
                                                                    <?= (isset($_POST['id_mk']) && $_POST['id_mk'] == $mk['id']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($mk['nama_mk']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="">Tidak ada data mata kuliah</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="deskripsi" class="form-label">
                                            <i class="fas fa-align-left me-2"></i>Deskripsi
                                        </label>
                                        <textarea class="form-control" name="deskripsi" id="deskripsi" rows="4" required><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="deadline" class="form-label">
                                                    <i class="fas fa-calendar-alt me-2"></i>Deadline
                                                </label>
                                                <input type="datetime-local" class="form-control" name="deadline" id="deadline" 
                                                       value="<?= isset($_POST['deadline']) ? htmlspecialchars($_POST['deadline']) : '' ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status" class="form-label">
                                                    <i class="fas fa-flag me-2"></i>Status
                                                </label>
                                                <select class="form-select" name="status" id="status" required>
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="belum" <?= (isset($_POST['status']) && $_POST['status'] == 'belum') ? 'selected' : '' ?>>
                                                        Belum Dikerjakan
                                                    </option>
                                                    <option value="selesai" <?= (isset($_POST['status']) && $_POST['status'] == 'selesai') ? 'selected' : '' ?>>
                                                        Selesai
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-user me-2"></i>User
                                        </label>
                                        <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($nama_lengkap) ?>" readonly>
                                        <small class="text-muted">Tugas akan otomatis ditambahkan untuk akun Anda</small>
                                    </div>
                                    
                                    <div class="btn-group-custom">
                                        <a href="/home.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Batal
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Tugas
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
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

            // Auto dismiss alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
<?php
mysqli_close($conn);
?>