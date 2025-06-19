<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();    // menghapus semua variabel session
session_destroy();  // menghancurkan session
header('Location: /login.php'); // arahkan ke halaman login
exit();
?>
