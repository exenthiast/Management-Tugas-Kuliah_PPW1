<?php 
include_once __DIR__ . '/../sidebarHandler/considebar.php'; 

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/');
}
?>

<div id="sidebar">
    <div class="sidebar-header">
        <h4>Manajemen Tugas</h4>
    </div>
    <ul class="components">
        <li>
            <a href="/home.php" class="<?= $currentPage == 'home.php' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Beranda</span>
            </a>
        </li>
        <li>
            <a href="/daftar-tugas.php" class="<?= $currentPage == 'daftar-tugas.php' ? 'active' : '' ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Daftar Tugas</span>
            </a>
        </li>
        <li>
            <a href="/jadwal-kuliah.php" class="<?= $currentPage == 'jadwal-kuliah.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Jadwal Kuliah</span>
            </a>
        </li>
        <li>
            <a href="/kalender.php" class="<?= $currentPage == 'kalender.php' ? 'active' : '' ?>">
                <i class="fas fa-calendar"></i>
                <span>Kalender</span>
            </a>
        </li>
    </ul>
</div>