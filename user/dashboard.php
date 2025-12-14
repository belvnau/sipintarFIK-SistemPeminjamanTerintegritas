<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

$user_id = $_SESSION['user_id'];
$query_total_barang = "SELECT COUNT(*) as total FROM items WHERE status = 'tersedia'";
$total_barang = mysqli_fetch_assoc(mysqli_query($conn, $query_total_barang))['total'];

$query_dipinjam = "SELECT COUNT(*) as total FROM borrowings WHERE user_id = $user_id AND status IN ('disetujui', 'dipinjam')";
$total_dipinjam = mysqli_fetch_assoc(mysqli_query($conn, $query_dipinjam))['total'];

$query_pending = "SELECT COUNT(*) as total FROM borrowings WHERE user_id = $user_id AND status = 'pending'";
$total_pending = mysqli_fetch_assoc(mysqli_query($conn, $query_pending))['total'];

$query_aktif = "SELECT b.*, i.nama_barang, i.kode_barang 
                FROM borrowings b 
                JOIN items i ON b.item_id = i.id 
                WHERE b.user_id = $user_id AND b.status IN ('disetujui', 'dipinjam')
                ORDER BY b.created_at DESC LIMIT 5";
$peminjaman_aktif = mysqli_query($conn, $query_aktif);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - sipintarFIK</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
    <style>
        :root { --upnvj-green: #1a472a; --upnvj-gold: #d4af37; }
        body { background: #f5f5f5; }
        .navbar { 
            background: var(--upnvj-green) !important;
            padding: 12px 0;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 20px;
        }
        .navbar-brand .logo-img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        .navbar-brand .brand-text {
            color: var(--upnvj-gold);
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .navbar-nav .nav-link {
            margin: 0 8px;
            padding: 8px 16px !important;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .navbar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            font-weight: 600;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid var(--upnvj-gold);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--upnvj-green);
            color: var(--upnvj-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .btn-upnvj { background: var(--upnvj-green); color: white; }
        .btn-upnvj:hover { background: #2d5a3d; color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <img src="../assets/img/logo-upnvj.png" alt="Logo UPNVJ" class="logo-img">
                <span class="brand-text">sipintarFIK</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="barang.php">
                            <i class="fas fa-box"></i> Daftar Barang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="peminjaman.php">
                            <i class="fas fa-clipboard-list"></i> Peminjaman Saya
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['nama']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="../logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="alert alert-success">
            <h4>Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h4>
            <p class="mb-0">Silakan ajukan peminjaman barang untuk kegiatan akademik Anda.</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $total_barang; ?></h3>
                            <small class="text-muted">Barang Tersedia</small>
                        </div>
                        <div class="stat-icon"><i class="fas fa-box"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $total_dipinjam; ?></h3>
                            <small class="text-muted">Sedang Dipinjam</small>
                        </div>
                        <div class="stat-icon"><i class="fas fa-clipboard-list"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $total_pending; ?></h3>
                            <small class="text-muted">Menunggu Persetujuan</small>
                        </div>
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Peminjaman Aktif</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($peminjaman_aktif) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Barang</th>
                                            <th>Tanggal Pinjam</th>
                                            <th>Kembali</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($peminjaman_aktif)): ?>
                                        <tr>
                                            <td><?php echo $row['kode_peminjaman']; ?></td>
                                            <td><?php echo $row['nama_barang']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td>
                                            <td>
                                                <?php if ($row['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Disetujui</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">Belum ada peminjaman aktif</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Menu Cepat</h5>
                    </div>
                    <div class="card-body">
                        <a href="barang.php" class="btn btn-upnvj w-100 mb-2">
                            <i class="fas fa-plus-circle"></i> Ajukan Peminjaman
                        </a>
                        <a href="peminjaman.php" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-history"></i> Riwayat Peminjaman
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h6>
                    </div>
                    <div class="card-body">
                        <small>
                            <strong>Ketentuan Peminjaman:</strong><br>
                            • Maksimal peminjaman 3 hari<br>
                            • Barang harus dikembalikan tepat waktu<br>
                            • Keterlambatan dikenakan sanksi
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>