<?php
include_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Proses update profil
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nama_kampus = mysqli_real_escape_string($conn, $_POST['nama_kampus']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);

    // Validasi
    if (empty($nama)) $errors[] = "Nama tidak boleh kosong";
    if (empty($email)) $errors[] = "Email tidak boleh kosong";
    if (empty($nama_kampus)) $errors[] = "Nama kampus tidak boleh kosong";
    if (empty($no_telepon)) $errors[] = "Nomor telepon tidak boleh kosong";

    // Cek email unik
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $id");
    if (mysqli_num_rows($check_email) > 0) {
        $errors[] = "Email sudah digunakan oleh user lain";
    }

    if (empty($errors)) {
        $query = "UPDATE users SET 
                 nama = '$nama', 
                 email = '$email', 
                 nama_kampus = '$nama_kampus', 
                 no_telepon = '$no_telepon',
                 updated_at = NOW()
                 WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $success = "Profil berhasil diperbarui!";
            // Refresh data user
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id");
            $users = mysqli_fetch_assoc($result);
            $nama_lengkap = $users['nama']; // Update nama untuk navbar
        } else {
            $errors[] = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }
}

// Proses update password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if (!password_verify($current_password, $users['password'])) {
        $errors[] = "Password saat ini salah";
    }
    if (empty($new_password)) {
        $errors[] = "Password baru tidak boleh kosong";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "Password baru minimal 6 karakter";
    }
    if ($new_password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = '$hashed_password' WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $success = "Password berhasil diubah!";
        } else {
            $errors[] = "Gagal mengubah password: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../assets/css/edit-profil.css">
</head>
<body>
    <div class="app-container">
        <?php include_once ROOT_PATH . 'components/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <?php include_once ROOT_PATH . 'components/navbar.php'; ?>

            <div class="content-body">
                <div class="container-fluid">
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/../profil.php">Profil</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Profil</li>
                        </ol>
                    </nav>

                    <!-- Alert Messages -->
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

                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-check-circle me-2"></i>Berhasil!</strong>
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <!-- Edit Profile Form -->
                            <div class="edit-profile-card">
                                <div class="section-header">
                                    <i class="fas fa-user-edit"></i>
                                    <h4>Edit Informasi Profil</h4>
                                </div>

                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nama" class="form-label">
                                                    <i class="fas fa-user me-2"></i>Nama Lengkap
                                                </label>
                                                <input type="text" class="form-control" id="nama" name="nama" 
                                                       value="<?= htmlspecialchars($users['nama']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="form-label">
                                                    <i class="fas fa-envelope me-2"></i>Email
                                                </label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?= htmlspecialchars($users['email']) ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nama_kampus" class="form-label">
                                                    <i class="fas fa-university me-2"></i>Nama Kampus
                                                </label>
                                                <input type="text" class="form-control" id="nama_kampus" name="nama_kampus" 
                                                       value="<?= htmlspecialchars($users['nama_kampus']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="no_telepon" class="form-label">
                                                    <i class="fas fa-phone me-2"></i>Nomor Telepon
                                                </label>
                                                <input type="tel" class="form-control" id="no_telepon" name="no_telepon" 
                                                       value="<?= htmlspecialchars($users['no_telepon']) ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group-custom">
                                        <a href="profil.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Kembali
                                        </a>
                                        <button type="submit" name="update_profile" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Change Password Form -->
                            <div class="edit-profile-card">
                                <div class="section-header">
                                    <i class="fas fa-lock"></i>
                                    <h4>Ubah Password</h4>
                                </div>

                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="current_password" class="form-label">
                                                    <i class="fas fa-key me-2"></i>Password Saat Ini
                                                </label>
                                                <input type="password" class="form-control" id="current_password" 
                                                       name="current_password" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="new_password" class="form-label">
                                                    <i class="fas fa-lock me-2"></i>Password Baru
                                                </label>
                                                <input type="password" class="form-control" id="new_password" 
                                                       name="new_password" minlength="6" required>
                                                <small class="form-text text-muted">
                                                    Password minimal 6 karakter
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="confirm_password" class="form-label">
                                                    <i class="fas fa-lock me-2"></i>Konfirmasi Password Baru
                                                </label>
                                                <input type="password" class="form-control" id="confirm_password" 
                                                       name="confirm_password" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group-custom">
                                        <button type="submit" name="update_password" class="btn btn-primary">
                                            <i class="fas fa-key me-2"></i>Ubah Password
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
    <script src="assets/js/edit-profil.js"></script>
</body>
</html>
<?php
mysqli_close($conn);
?>