<?php
// File: index.php
// Fungsi: Halaman Login

// Mulai session untuk menyimpan data login user
session_start();

// Include koneksi database
include 'config/database.php';

// Cek jika user sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}

// Variable untuk pesan error
$error = '';

// Proses login ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Query untuk cek user di database
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    // Cek apakah email ditemukan
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil, simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect sesuai role
            if ($user['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: user/dashboard.php');
            }
            exit();
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Email tidak ditemukan!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - sipintarFIK</title>
    
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/fontawesome/css/all.min.css" rel="stylesheet">
    
    <style>
           body {
                background: linear-gradient(135deg, #5a8a6f 0%, #88c9a1 100%);
                background-image: 
                    radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px),
                    linear-gradient(135deg, #7a9b7f 0%, #d3e6d4ff 100%);
                background-size: 20px 20px, 100% 100%;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .login-card {
                background: white;
                border-radius: 20px;
                box-shadow: 0 15px 50px rgba(0,0,0,0.15);
                overflow: hidden;
                max-width: 950px;
                width: 100%;
                display: flex;
            }
            .login-left {
                background: linear-gradient(135deg, #1a472a 0%, #2d5a3d 100%);
                color: white;
                padding: 50px 40px;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
            }
            .logo-container {
                margin-bottom: px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
            }
            .logo-img {
                width: 80px;
                height: 80px;
                object-fit: contain;
                filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
            }
            .brand-name {
                font-size: 42px;
                font-weight: bold;
                color: #ffd700;
                margin-bottom: 0;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
            }
            .brand-subtitle {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 10px;
            }
            .university-name {
                font-size: 14px;
                opacity: 0.9;
                line-height: 1.5;
            }
            .login-right {
                padding: 50px 45px;
                flex: 1;
            }
            .login-title {
                color: #1a472a;
                font-weight: 700;
                margin-bottom: 30px;
                font-size: 28px;
            }
            .form-label {
                color: #2d5a3d;
                font-weight: 600;
                margin-bottom: 8px;
            }
            .form-control {
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                padding: 12px 15px;
                transition: all 0.3s;
            }
            .form-control:focus {
                border-color: #2d5a3d;
                box-shadow: 0 0 0 0.2rem rgba(45, 90, 61, 0.15);
            }
            .btn-upnvj {
                background: linear-gradient(135deg, #1a472a 0%, #2d5a3d 100%);
                color: white;
                border: none;
                padding: 14px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 16px;
                transition: all 0.3s;
            }
            .btn-upnvj:hover {
                background: linear-gradient(135deg, #0f2f1a 0%, #1a472a 100%);
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(26, 71, 42, 0.3);
            }
            .demo-accounts {
                background: #ffffffff;
                border-radius: 10px;
                padding: 20px;
                margin-top: 25px;
            }
            .demo-accounts strong {
                color: #1a472a;
            }
            .demo-accounts p {
                margin-bottom: 5px;
                font-size: 13px;
            }
            .alert {
                border-radius: 8px;
                border: none;
            }
            
            @media (max-width: 768px) {
                .login-card {
                    flex-direction: column;
                }
                .login-left, .login-right {
                    padding: 30px 25px;
                }
                .brand-name {
                    font-size: 32px;
                }
                .logo-img {
                    width: 90px;
                    height: 90px;
                }
        }
    </style>
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-left">
            <div class="logo-container">
                <img src="assets/img/logo-upnvj.png" alt="Logo UPNVJ" class="logo-img">
                <div class="brand-name">sipintarFIK</div>
            </div>
            <div style=""></div>
            <div class="brand-subtitle">
                <strong>Sistem Peminjaman Barang Terintegrasi<br>
                Fakultas Ilmu Komputer
                UPN "Veteran" Jakarta</strong>
            </div>
        </div>
        
        <div class="login-right">
            <h3 class="login-title">
                <i class="fas fa-sign-in-alt"></i> Login
            </h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" class="form-control" required 
                           placeholder="contoh@upnvj.ac.id">
                </div>
                
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" name="password" class="form-control" required 
                           placeholder="Masukkan password">
                </div>
                
                <button type="submit" class="btn btn-upnvj w-100">
                    <i class="fas fa-sign-in-alt"></i> Login Sekarang
                </button>
            </form>
            
            <div class="demo-accounts">
                <p class="mb-2 text-center"><strong>Akun Demo:</strong></p>
                <p class="mb-1 text-muted text-center"><i class="fas fa-user-shield"></i> Admin: admin@upnvj.ac.id / password</p>
                <p class="mb-0 text-muted text-center"><i class="fas fa-user"></i> User: budi@student.upnvj.ac.id / password</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>