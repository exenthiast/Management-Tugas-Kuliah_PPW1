<?php
include_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Cek apakah ada ID tugas yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: daftar-tugas.php');
    exit();
}

$id_tugas = intval($_GET['id']);
$message = '';
$messageType = '';

// Ambil data tugas berdasarkan ID
$sql = "SELECT t.*, mk.nama_mk, mk.jenis 
        FROM tugas t 
        JOIN mata_kuliah mk ON t.id_mk = mk.id 
        WHERE t.id = '$id_tugas' AND t.id_user = '$user_id'";
$result = $conn->query($sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: daftar-tugas.php');
    exit();
}

$tugas = mysqli_fetch_assoc($result);

// Ambil daftar mata kuliah untuk dropdown
$sql_mk = "SELECT id, nama_mk, jenis FROM mata_kuliah WHERE id_user = '$user_id' ORDER BY nama_mk";
$result_mk = $conn->query($sql_mk);

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_mk = intval($_POST['id_mk']);
    $deskripsi = trim($_POST['deskripsi']);
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];
    
    // Validasi input
    if (empty($id_mk) || empty($deskripsi) || empty($deadline) || empty($status)) {
        $message = 'Semua field harus diisi!';
        $messageType = 'danger';
    } else {
        // Update data tugas
        $sql_update = "UPDATE tugas SET 
                       id_mk = '$id_mk',
                       deskripsi = '" . mysqli_real_escape_string($conn, $deskripsi) . "',
                       deadline = '$deadline',
                       status = '" . mysqli_real_escape_string($conn, $status) . "'
                       WHERE id = '$id_tugas' AND id_user = '$user_id'";
        
        if ($conn->query($sql_update)) {
            $message = 'Tugas berhasil diperbarui!';
            $messageType = 'success';
            
            // Refresh data tugas
            $result = $conn->query("SELECT t.*, mk.nama_mk, mk.jenis 
                                   FROM tugas t 
                                   JOIN mata_kuliah mk ON t.id_mk = mk.id 
                                   WHERE t.id = '$id_tugas' AND t.id_user = '$user_id'");
            $tugas = mysqli_fetch_assoc($result);
        } else {
            $message = 'Gagal memperbarui tugas: ' . $conn->error;
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/edit-tugas.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include_once ROOT_PATH . 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <?php include_once ROOT_PATH . 'components/navbar.php'; ?>

            <!-- Content Body -->
            <div class="container-fluid">
                <div class="edit-card">
                    <div class="page-header">
                        <h2>Edit Tugas</h2>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                            <i class="fas fa-<?php echo $messageType == 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_mk" class="form-label">
                                    <i class="fas fa-book text-primary me-2"></i>Mata Kuliah
                                </label>
                                <select class="form-select" id="id_mk" name="id_mk" required>
                                    <option value="">Pilih Mata Kuliah</option>
                                    <?php
                                    if ($result_mk && mysqli_num_rows($result_mk) > 0) {
                                        while ($mk = mysqli_fetch_assoc($result_mk)) {
                                            $selected = ($mk['id'] == $tugas['id_mk']) ? 'selected' : '';
                                            echo "<option value='" . $mk['id'] . "' $selected>" . htmlspecialchars($mk['nama_mk']) . " (" . htmlspecialchars($mk['jenis']) . ")</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-flag text-primary me-2"></i>Status
                                </label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="belum selesai" <?php echo ($tugas['status'] == 'belum selesai') ? 'selected' : ''; ?>>Belum Selesai</option>
                                    <option value="selesai" <?php echo ($tugas['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                                    <option value="terlambat" <?php echo ($tugas['status'] == 'terlambat') ? 'selected' : ''; ?>>Terlambat</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">
                                <i class="fas fa-align-left text-primary me-2"></i>Deskripsi Tugas
                            </label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" placeholder="Masukkan deskripsi tugas..." required><?php echo htmlspecialchars($tugas['deskripsi']); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="deadline" class="form-label">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Deadline
                            </label>
                            <input type="datetime-local" class="form-control" id="deadline" name="deadline" value="<?php echo date('Y-m-d\TH:i', strtotime($tugas['deadline'])); ?>" required>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="daftar-tugas.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/edit-tugas.js"></script>
</body>
</html>
<?php
$conn->close();
?>