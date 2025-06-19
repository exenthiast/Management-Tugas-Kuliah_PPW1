<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['id'] ?? null;
$users = null;

if ($user_id) {
    $query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
    if ($query) {
        $users = mysqli_fetch_assoc($query);
    }
}

if (!$users) {
    // Redirect ke halaman login jika user tidak ditemukan
    header("Location: login.php");
    exit();
}
?>