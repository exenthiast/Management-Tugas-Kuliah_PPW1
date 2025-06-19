<?php
include_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

$success = '';
$errors = [];

// Validasi ID jadwal
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /jadwal-kuliah.php');
    exit();
}

$id_jadwal = (int)$_GET['id'];

// Ambil data jadwal yang akan diedit
$jadwal_query = "SELECT j.*, mk.nama_mk, mk.jenis 
                FROM jadwal j 
                JOIN mata_kuliah mk ON j.id_mk = mk.id 
                WHERE j.id = $id_jadwal AND mk.id_user = '$user_id'";
$jadwal_result = mysqli_query($conn, $jadwal_query);

if (!$jadwal_result || mysqli_num_rows($jadwal_result) == 0) {
    header('Location: /jadwal-kuliah.php');
    exit();
}

$jadwal_data = mysqli_fetch_assoc($jadwal_result);

// Ambil daftar mata kuliah untuk dropdown
$mk_query = mysqli_query($conn, "SELECT id, nama_mk FROM mata_kuliah WHERE id_user = '$user_id' ORDER BY nama_mk ASC");
if (!$mk_query) {
    die('Error query mata kuliah: ' . mysqli_error($conn));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mk = $_POST['id_mk'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    
    // Validasi input
    if (empty($id_mk)) {
        $errors[] = "Mata kuliah harus dipilih!";
    }
    if (empty($hari)) {
        $errors[] = "Hari harus dipilih!";
    }
    if (empty($jam_mulai)) {
        $errors[] = "Jam mulai harus diisi!";
    }
    if (empty($jam_selesai)) {
        $errors[] = "Jam selesai harus diisi!";
    }
    
    // Validasi jam mulai tidak boleh lebih besar dari jam selesai
    if (!empty($jam_mulai) && !empty($jam_selesai)) {
        if (strtotime($jam_mulai) >= strtotime($jam_selesai)) {
            $errors[] = "Jam mulai harus lebih kecil dari jam selesai!";
        }
    }
    
    // Cek konflik jadwal (kecuali dengan jadwal yang sedang diedit)
    if (empty($errors)) {
        $conflict_query = "SELECT j.id 
                          FROM jadwal j 
                          JOIN mata_kuliah mk ON j.id_mk = mk.id 
                          WHERE mk.id_user = '$user_id' 
                          AND j.id != $id_jadwal
                          AND j.hari = '$hari' 
                          AND (
                              ('$jam_mulai' >= j.jam_mulai AND '$jam_mulai' < j.jam_selesai) OR
                              ('$jam_selesai' > j.jam_mulai AND '$jam_selesai' <= j.jam_selesai) OR
                              ('$jam_mulai' <= j.jam_mulai AND '$jam_selesai' >= j.jam_selesai)
                          )";
        
        $conflict_result = mysqli_query($conn, $conflict_query);
        if (mysqli_num_rows($conflict_result) > 0) {
            $errors[] = "Jadwal bentrok dengan jadwal yang sudah ada pada hari dan jam tersebut!";
        }
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        $update_query = "UPDATE jadwal SET 
                        id_mk = '$id_mk',
                        hari = '$hari',
                        jam_mulai = '$jam_mulai',
                        jam_selesai = '$jam_selesai'
                        WHERE id = $id_jadwal";
        
        if (mysqli_query($conn, $update_query)) {
            $success = "Jadwal berhasil diperbarui!";
            // Refresh data jadwal setelah update
            $jadwal_result = mysqli_query($conn, $jadwal_query);
            $jadwal_data = mysqli_fetch_assoc($jadwal_result);
        } else {
            $errors[] = "Gagal memperbarui jadwal: " . mysqli_error($conn);
        }
    }
}

$hari_options = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Kuliah - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/edit-jadwal.css">
</head>
<body>
    <div class="app-container">        <!-- Sidebar -->
        <?php include_once ROOT_PATH . 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
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

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Jadwal Kuliah
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="id_mk" class="form-label">
                                            <i class="fas fa-book me-1"></i>
                                            Mata Kuliah <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="id_mk" name="id_mk" required>
                                            <option value="">Pilih Mata Kuliah</option>
                                            <?php while ($mk = mysqli_fetch_assoc($mk_query)): ?>
                                                <option value="<?= $mk['id'] ?>" 
                                                    <?= ($mk['id'] == $jadwal_data['id_mk']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($mk['nama_mk']) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="hari" class="form-label">
                                            <i class="fas fa-calendar me-1"></i>
                                            Hari <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="hari" name="hari" required>
                                            <option value="">Pilih Hari</option>
                                            <?php foreach ($hari_options as $hari): ?>
                                                <option value="<?= $hari ?>" 
                                                    <?= ($hari == $jadwal_data['hari']) ? 'selected' : '' ?>>
                                                    <?= $hari ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_mulai" class="form-label">
                                            <i class="fas fa-clock me-1"></i>
                                            Jam Mulai <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" 
                                               value="<?= htmlspecialchars($jadwal_data['jam_mulai']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jam_selesai" class="form-label">
                                            <i class="fas fa-clock me-1"></i>
                                            Jam Selesai <span class="text-danger">*</span>
                                        </label>
                                        <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" 
                                               value="<?= htmlspecialchars($jadwal_data['jam_selesai']) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Simpan Perubahan
                                </button>
                                <a href="/jadwal-kuliah.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/edit-jadwal.js"></script>
</body>
</html>