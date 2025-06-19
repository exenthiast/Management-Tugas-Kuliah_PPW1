<?php
include_once dirname(__DIR__) . '/includes/get_user_profile.php';
?>
<nav class="top-navbar">
    <button type="button" id="sidebarCollapse" class="btn btn-sidebar-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <div class="user-dropdown">
        <div class="dropdown">
            <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle fa-lg me-2"></i>
                <span class="d-none d-sm-inline"><?= htmlspecialchars($nama_lengkap) ?></span>
            </a>
            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="profil.php"><i class="fas fa-user-cog me-2"></i>Profil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</nav>