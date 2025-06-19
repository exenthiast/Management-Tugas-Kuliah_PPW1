<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('BASE_URL')) {
    define('BASE_URL', '/manajemen-tugas-kuliah/'); 
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/config.php';
$success = '';
$errors = [];

include_once __DIR__ . '/handler/handler_register.php';
include_once __DIR__ . '/handler/handler_login.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Manajemen Tugas Kuliah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="assets/css/login.css" />
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background: linear-gradient(180deg, #6a5af9 0%, #a259f7 50%, #4fc3f7 100%);
            font-family: Arial, sans-serif;
        }
        .login-container {
            margin-top: 100px;
        }
        .login-form {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 24px;
            margin-bottom: 24px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 16px;
        }
        .login-form-title {
            text-align: center;
            margin-bottom: 24px;
            color: #6a5af9;
        }
        .btn-login {
            width: 100%;
            background: #6a5af9;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            padding: 12px;
            margin-top: 8px;
        }
        .btn-login:hover {
            background: #4f46e5;
        }
        .toggle-form {
            text-align: center;
            margin-top: 16px;
        }
    </style>
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
                <ul class="navbar-nav ms-auto"></ul>
            </div>
        </div>
    </nav>

    <!-- Login & Register Container -->
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $e): ?>
                                <li><?= $e ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <div class="login-form" id="loginForm">
                    <div class="login-logo">
                        <i class="fas fa-tasks fa-5x text-primary"></i>
                    </div>
                    <h2 class="login-form-title">Login</h2>
                    <form id="loginFormSubmit" action="" method="post">
                        <input type="hidden" name="action" value="login">
                        <div class="form-group mb-3">
                            <label for="loginEmail">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="loginEmail" name="email" 
                                       placeholder="Masukkan email" required />
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="loginPassword">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="loginPassword" name="password" 
                                    placeholder="Masukkan password" required />
                                <span class="input-group-text toggle-password" data-target="#loginPassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>
                    </form>
                    <div class="toggle-form">
                        <p>Belum punya akun? <a href="#" id="showRegister">Daftar sekarang</a></p>
                    </div>
                </div>

                <!-- Register Form -->
                <div class="login-form" id="registerForm" style="display: none">
                    <div class="login-logo">
                        <i class="fas fa-user-plus fa-5x text-primary"></i>
                    </div>
                    <h2 class="login-form-title">Register</h2>
                    <form id="registerFormSubmit" action="" method="post">
                        <input type="hidden" name="action" value="register">
                        <div class="form-group mb-3">
                            <label for="regName">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="regName" name="nama" 
                                       placeholder="Masukkan nama lengkap" required />
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="regEmail">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="regEmail" name="email" 
                                       placeholder="Masukkan email" required />
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="regPassword">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="regPassword" name="password" 
                                    placeholder="Masukkan password (min. 6 karakter)" required minlength="6" />
                                <span class="input-group-text toggle-password" data-target="#regPassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="regUniversity">Nama Kampus</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-university"></i></span>
                                <input type="text" class="form-control" id="regUniversity" name="nama_kampus" 
                                       placeholder="Masukkan nama kampus" required />
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="regPhone">Nomor Telepon</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="regPhone" name="no_telepon" 
                                       placeholder="Masukkan nomor telepon" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-user-plus"></i> Register
                            </button>
                        </div>
                    </form>
                    <div class="toggle-form">
                        <p>Sudah punya akun? <a href="#" id="showLogin">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).on('click', '.toggle-password', function () {
            const input = $($(this).data('target'));
            const icon = $(this).find('i');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $(document).ready(function() {
            $('#showRegister').click(function(e) {
                e.preventDefault();
                $('#loginForm').slideUp(300, function() {
                    $('#registerForm').slideDown(300);
                });
            });
            
            $('#showLogin').click(function(e) {
                e.preventDefault();
                $('#registerForm').slideUp(300, function() {
                    $('#loginForm').slideDown(300);
                });
            });
        });
    </script>
</body>
</html>