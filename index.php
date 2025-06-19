<?php
session_start();
require_once __DIR__ . '/config/config.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Management Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/img/logo.png" alt="Logo">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-info-circle me-1"></i>About
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="https://instagram.com/d.destaz" target="_blank">
                                    <i class="fab fa-instagram text-danger"></i>Instagram
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://www.tiktok.com/@apizsuka99" target="_blank">
                                    <i class="fab fa-tiktok text-dark"></i>TikTok
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://www.linkedin.com/in/alfiz-desta-0a0318326/" target="_blank">
                                    <i class="fab fa-linkedin text-primary"></i>LinkedIn
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-envelope me-1"></i>Kontak
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="mailto:alfizdestaap@gmail.com?subject=Pertanyaan%20Penting&body=Halo,%20saya%20ingin%20menanyakan%20hal%20berikut...">
                                    <i class="fas fa-envelope text-primary"></i>Email
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://wa.me/62895383012920?text=Halo%20Alfiz,%20saya%20ingin%20menanyakan%20hal%20berikut...">
                                    <i class="fa-brands fa-whatsapp"></i>Whatsapp
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-background"></div>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Selamat datang di Management Tugas Kuliah</h1>
            <p>Website Management Tugas Kuliah adalah aplikasi yang dirancang untuk membantu mahasiswa 
                        dalam mengelola tugas dan jadwal kuliah secara efektif. Dibuat dengan menggunakan 
                        teknologi modern untuk pengalaman pengguna yang optimal.</p>
            <div class="hero-buttons">
                <?php if (isset($_SESSION['id'])): ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Mulai Sekarang
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>MTK - Management Tugas Kuliah</h5>
                    <p class="mb-0">Universitas Gadjah Mada</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2025 Alfiz Desta. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.backgroundColor = 'var(--primary-dark)';
            } else {
                navbar.style.backgroundColor = 'var(--primary-color)';
            }
        });
    </script>
</body>
</html>