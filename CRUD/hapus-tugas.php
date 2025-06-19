<?php
include_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/config/config.php';
include_once dirname(__DIR__) . '/includes/get_user_info.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Cek apakah ID ada dalam query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "ID tugas tidak ditemukan.";
    header('Location: /daftar-tugas.php');
    exit();
}

$id = intval($_GET['id']); // Menghindari SQL Injection sederhana

// Ambil user_id yang sedang login
include_once __DIR__ . '/includes/get_user_info.php';

// Cek apakah tugas tersebut memang milik user yang sedang login
$checkQuery = "SELECT * FROM tugas WHERE id = $id AND id_user = $user_id";
$result = $conn->query($checkQuery);

if ($result && $result->num_rows > 0) {
    // Lakukan penghapusan
    $deleteQuery = "DELETE FROM tugas WHERE id = $id";
    if ($conn->query($deleteQuery) === TRUE) {
        $_SESSION['success'] = "Tugas berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus tugas: " . $conn->error;
    }
} else {
    $_SESSION['error'] = "Tugas tidak ditemukan atau bukan milik Anda.";
}

$conn->close();
header('Location: /daftar-tugas.php');
exit();
?>