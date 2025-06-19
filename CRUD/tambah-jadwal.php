<?php
include_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/config/config.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}


include_once dirname(__DIR__) . '/includes/get_user_info.php';

// Query untuk mendapatkan daftar mata kuliah berdasarkan user yang login
$mk_query = mysqli_query($conn, "SELECT id, nama_mk FROM mata_kuliah WHERE id_user = '$user_id' ORDER BY nama_mk ASC");
if (!$mk_query) {
    die('Error query mata kuliah: ' . mysqli_error($conn));
}

$success = '';
$errors = [];

if (isset($_POST['submit'])) {
    $id_mk = $_POST['id_mk'] ?? '';
    $hari = $_POST['hari'] ?? '';
    $jam_mulai = $_POST['jam_mulai'] ?? '';
    $jam_selesai = $_POST['jam_selesai'] ?? '';
    $id_user = $user_id; // Menggunakan ID user yang sudah login

    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Validasi
    if (empty($id_mk)) $errors[] = "Mata kuliah harus dipilih";
    if (empty($hari)) $errors[] = "Hari harus dipilih";
    if (empty($jam_mulai)) $errors[] = "Jam mulai tidak boleh kosong";
    if (empty($jam_selesai)) $errors[] = "Jam selesai tidak boleh kosong";
    
    // Validasi jam mulai tidak boleh lebih besar dari jam selesai
    if (!empty($jam_mulai) && !empty($jam_selesai)) {
        if ($jam_mulai >= $jam_selesai) {
            $errors[] = "Jam mulai harus lebih kecil dari jam selesai";
        }
    }

    if (empty($errors)) {
        // Cek apakah sudah ada jadwal yang bentrok di hari dan jam yang sama
        $check_query = mysqli_prepare($conn, "SELECT id FROM jadwal WHERE id_user = ? AND hari = ? AND ((jam_mulai <= ? AND jam_selesai > ?) OR (jam_mulai < ? AND jam_selesai >= ?) OR (jam_mulai >= ? AND jam_selesai <= ?))");
        mysqli_stmt_bind_param($check_query, "isssssss", $id_user, $hari, $jam_mulai, $jam_mulai, $jam_selesai, $jam_selesai, $jam_mulai, $jam_selesai);
        mysqli_stmt_execute($check_query);
        $check_result = mysqli_stmt_get_result($check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Sudah ada jadwal lain pada hari dan jam yang bentrok";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO jadwal (hari, jam_mulai, jam_selesai, id_mk, id_user, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                die('Error prepare statement: ' . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "sssiiss", $hari, $jam_mulai, $jam_selesai, $id_mk, $id_user, $created_at, $updated_at);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Jadwal berhasil ditambahkan.";
                // Reset form values
                $_POST = array();
            } else {
                $errors[] = "Gagal menambahkan jadwal: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_query);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal - Management Jadwal Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/tambah-jadwal.css">
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
                        <h5><i class="fas fa-calendar-plus me-2"></i>Tambah Jadwal Kuliah</h5>
                        <p>Tambahkan jadwal kuliah baru dengan mengisi form berikut😍.</p>
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
                                    <h4>Form Tambah Jadwal</h4>
                                </div>

                                <?php if (mysqli_num_rows($mk_query) == 0): ?>
                                    <div class="no-mk-alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Perhatian!</strong> Anda belum memiliki mata kuliah. Silakan tambahkan mata kuliah terlebih dahulu sebelum membuat jadwal.
                                        <br><br>
                                        <a href="tambah-matkul.php" class="btn btn-warning btn-sm">
                                        <i class="fas fa-plus me-2"></i>Tambah Mata Kuliah
                                        </a>
                                    </div>
                                <?php else: ?>

                                <form method="post" action="">
                                    <div class="form-group">
                                        <label for="id_mk" class="form-label">
                                            <i class="fas fa-book me-2"></i>Mata Kuliah
                                        </label>
                                        <select class="form-select" name="id_mk" id="id_mk" required>
                                            <option value="">-- Pilih Mata Kuliah --</option>
                                            <?php 
                                            mysqli_data_seek($mk_query, 0); // Reset pointer ke awal
                                            while ($mk = mysqli_fetch_assoc($mk_query)): 
                                            ?>
                                                <option value="<?= $mk['id'] ?>" <?= (isset($_POST['id_mk']) && $_POST['id_mk'] == $mk['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($mk['nama_mk']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <small class="text-muted">Pilih mata kuliah yang akan dijadwalkan</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="hari" class="form-label">
                                            <i class="fas fa-calendar-day me-2"></i>Hari
                                        </label>
                                        <select class="form-select" name="hari" id="hari" required>
                                            <option value="">-- Pilih Hari --</option>
                                            <option value="senin" <?= (isset($_POST['hari']) && $_POST['hari'] == 'senin') ? 'selected' : '' ?>>
                                                Senin
                                            </option>
                                            <option value="selasa" <?= (isset($_POST['hari']) && $_POST['hari'] == 'selasa') ? 'selected' : '' ?>>
                                                Selasa
                                            </option>
                                            <option value="rabu" <?= (isset($_POST['hari']) && $_POST['hari'] == 'rabu') ? 'selected' : '' ?>>
                                                Rabu
                                            </option>
                                            <option value="kamis" <?= (isset($_POST['hari']) && $_POST['hari'] == 'kamis') ? 'selected' : '' ?>>
                                                Kamis
                                            </option>
                                            <option value="jumat" <?= (isset($_POST['hari']) && $_POST['hari'] == 'jumat') ? 'selected' : '' ?>>
                                                Jumat
                                            </option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-clock me-2"></i>Waktu Pelaksanaan
                                        </label>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label for="jam_mulai" class="form-label text-muted small">Jam Mulai</label>
                                                <input type="time" class="form-control" name="jam_mulai" id="jam_mulai" 
                                                       value="<?= isset($_POST['jam_mulai']) ? htmlspecialchars($_POST['jam_mulai']) : '' ?>" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end justify-content-center">
                                                <span class="time-separator mb-2">s/d</span>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="jam_selesai" class="form-label text-muted small">Jam Selesai</label>
                                                <input type="time" class="form-control" name="jam_selesai" id="jam_selesai" 
                                                       value="<?= isset($_POST['jam_selesai']) ? htmlspecialchars($_POST['jam_selesai']) : '' ?>" required>
                                            </div>
                                        </div>
                                        <small class="text-muted">Pilih waktu mulai dan selesai kegiatan</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-user me-2"></i>Pengguna
                                        </label>
                                        <input type="text" class="form-control readonly-field" value="<?= htmlspecialchars($nama_lengkap) ?>" readonly>
                                        <small class="text-muted">Jadwal akan otomatis ditambahkan untuk akun Anda</small>
                                    </div>
                                    
                                    <div class="btn-group-custom">
                                        <a href="/jadwal-kuliah.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Batal
                                        </a>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Jadwal
                                        </button>
                                    </div>
                                </form>

                                <?php endif; ?>
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
    <script src="assets/js/tambah-jadwal.js"></script>
</body>
</html>
<?php
mysqli_close($conn);
?>