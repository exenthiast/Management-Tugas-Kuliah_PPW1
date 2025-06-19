<?php
$host = "localhost";
$dbname = "u985354573_studymate";
$username = "u985354573_Alfiz";
$password = "Aldean0112";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Konstanta untuk database (bisa digunakan di bagian lain aplikasi)
define("DB_HOST", $host);
define("DB_USER", $username);
define("DB_PASS", $password);
define("DB_NAME", $dbname);

// Set zona waktu
date_default_timezone_set('Asia/Jakarta');

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi MySQLi gagal: " . $conn->connect_error);
}

// Daftar hari libur nasional
$libur_nasional = [
    '2025-01-01' => 'Tahun Baru Masehi',
    '2025-02-29' => 'Tahun Baru Imlek',
    '2025-03-29' => 'Hari Raya Nyepi',
    '2025-04-18' => 'Wafat Isa Almasih',
    '2025-05-01' => 'Hari Buruh Internasional',
    '2025-05-12' => 'Hari Raya Waisak',
    '2025-05-29' => 'Kenaikan Isa Almasih',
    '2025-06-01' => 'Hari Lahir Pancasila',
    '2025-06-06' => 'Hari Raya Idul Adha',
    '2025-06-27' => 'Tahun Baru Islam 1 Muharram 1447H',
    '2025-07-07' => 'Tahun Baru Islam',
    '2025-08-17' => 'Hari Kemerdekaan RI ke-80',
    '2025-10-05' => 'Maulid Nabi Muhammad SAW',
    '2025-12-25' => 'Hari Raya Natal',
    '2025-12-26' => 'Cuti Bersama Hari Raya Natal',
];
?>