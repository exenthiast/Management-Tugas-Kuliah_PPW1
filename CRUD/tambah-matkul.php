<?php
include_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

$query_mk = mysqli_query($conn, "SELECT * FROM mata_kuliah WHERE id_user = '$user_id' ORDER BY created_at DESC");

$success = '';
$errors = [];

if (isset($_POST['submit'])) {
    $nama_mk = trim($_POST['mata_kuliah'] ?? '');
    $jenis = $_POST['jenis'] ?? '';
    $id_user = $user_id; // Menggunakan ID user yang sudah login

    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Validasi
    if (empty($nama_mk)) {
        $errors[] = "Nama Mata Kuliah tidak boleh kosong";
    }
    if (empty($jenis)) {
        $errors[] = "Silahkan pilih Praktikum atau Teori";
    }

    if (empty($errors)) {

        $stmt = mysqli_prepare($conn, "INSERT INTO mata_kuliah (nama_mk, jenis, id_user, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die('Error prepare statement: ' . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($stmt, "sssss", $nama_mk, $jenis, $id_user, $created_at, $updated_at);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Mata Kuliah berhasil ditambahkan.";
            $_POST = array(); // Reset form values
        } else {
            $errors[] = "Gagal menambahkan Mata Kuliah: " . mysqli_stmt_error($stmt);
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
    <title>Tambah Mata Kuliah - Management Jadwal Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tambah-matkul.css">
</head>
<body>
    <div class="app-container">
        <?php include_once ROOT_PATH . 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <?php include_once ROOT_PATH . 'components/navbar.php'; ?>

            <div class="content-body">
                <div class="container-fluid">

                    <!-- Info Card -->
                    <div class="info-card">
                        <h5><i class="fas fa-calendar-plus me-2"></i>Tambah Mata Kuliah</h5>
                        <p>Tambahkan mata kuliah baru dengan mengisi form berikut😍.</p>
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
                            <!-- Form Tambah Jadwal -->
                            <div class="form-card">
                                <div class="section-header">
                                    <i class="fas fa-calendar-plus"></i>
                                    <h4>Form Tambah Mata Kuliah</h4>
                                </div>

                                <form method="post" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mata_kuliah" class="form-label">
                                                    <i class="fas fa-book me-2"></i>Mata Kuliah
                                                </label>
                                                <input type="text" class="form-control" name="mata_kuliah" id="mata_kuliah" 
                                                       placeholder="Contoh: Pemrograman Web"
                                                       value="<?= isset($_POST['nama_mk']) ? htmlspecialchars($_POST['nama_mk']) : '' ?>" required>
                                                <small class="text-muted">Masukkan nama mata kuliah</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="jenis" class="form-label">
                                                    <i class="fas fa-tags me-2"></i>Jenis Kegiatan
                                                </label>
                                                <select class="form-select" name="jenis" id="jenis" required>
                                                    <option value="">-- Pilih Jenis --</option>
                                                    <option value="teori" <?= (isset($_POST['jenis']) && $_POST['jenis'] == 'teori') ? 'selected' : '' ?>>
                                                        Teori
                                                    </option>
                                                    <option value="praktikum" <?= (isset($_POST['jenis']) && $_POST['jenis'] == 'praktikum') ? 'selected' : '' ?>>
                                                        Praktikum
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-user me-2"></i>Pengguna
                                        </label>
                                        <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($nama_lengkap) ?>" readonly>
                                        <small class="text-muted">Jadwal akan otomatis ditambahkan untuk akun Anda</small>
                                    </div>
                                    
                                    <div class="btn-group-custom">
                                        <a href="/home.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Batal
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Jadwal
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
    <script src="assets/js/tambah-matkul.js"></script>
</body>
</html>
<?php
mysqli_close($conn);
?>