<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan $pdo sudah dibuat sebelumnya melalui koneksi PDO

if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $errors = [];

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validasi input sederhana
    if (empty($email)) $errors[] = "Email tidak boleh kosong.";
    if (empty($password)) $errors[] = "Password tidak boleh kosong.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];

            header("Location: home.php");
            exit();
        } else {
            $errors[] = "Email atau password salah.";
        }
    }
}
?>
