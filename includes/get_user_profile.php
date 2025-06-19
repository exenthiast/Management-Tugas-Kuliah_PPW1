<?php
$id = $_SESSION['id'];

// Ambil data user
$query = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $query);
$users = mysqli_fetch_assoc($result);

// Ambil nama untuk navbar
$nama_lengkap = $users['nama'] ?? 'User';
?>