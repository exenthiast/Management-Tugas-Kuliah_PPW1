<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi PDO sudah dibuat sebelumnya dan disimpan dalam variabel $pdo
// Contoh di file config:
// $pdo = new PDO("mysql:host=localhost;dbname=namadb", "user", "pass");

if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $errors = [];
    $success = '';

    $nama         = trim($_POST['nama']);
    $email        = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $nama_kampus  = trim($_POST['nama_kampus']);
    $no_telepon   = trim($_POST['no_telepon']);
    $created_at   = date('Y-m-d H:i:s');
    $updated_at   = date('Y-m-d H:i:s');

    // Validasi
    if (empty($nama)) $errors[] = "Nama tidak boleh kosong";
    if (empty($email)) $errors[] = "Email tidak boleh kosong";
    if (empty($password_raw)) $errors[] = "Password tidak boleh kosong";
    if (strlen($password_raw) < 6) $errors[] = "Password minimal 6 karakter";
    if (empty($nama_kampus)) $errors[] = "Nama Kampus tidak boleh kosong";
    if (empty($no_telepon)) $errors[] = "Nomor Telepon tidak boleh kosong";

    // Cek email terdaftar
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $errors[] = "Email sudah terdaftar";
        }
    }

    // Eksekusi INSERT jika tidak ada error
    if (empty($errors)) {
        $hashed_password = password_hash($password_raw, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (nama, email, password, nama_kampus, no_telepon, created_at, updated_at)
                  VALUES (:nama, :email, :password, :nama_kampus, :no_telepon, :created_at, :updated_at)";

        $stmt = $pdo->prepare($query);
        $result = $stmt->execute([
            'nama'        => $nama,
            'email'       => $email,
            'password'    => $hashed_password,
            'nama_kampus' => $nama_kampus,
            'no_telepon'  => $no_telepon,
            'created_at'  => $created_at,
            'updated_at'  => $updated_at
        ]);

        if ($result) {
            $success = "Registrasi berhasil! Silakan login.";
            $_POST = array(); // Bersihkan form
        } else {
            $errors[] = "Gagal menambahkan data.";
        }
    }
}
?>
