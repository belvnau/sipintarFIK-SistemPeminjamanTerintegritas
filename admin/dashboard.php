<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

// Statistik
$total_barang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM items"))['total'];
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
$total_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowings"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowings WHERE status='pending'"))['total'];

// Peminjaman pending
$query_pending = "SELECT b.*, u.nama, i.nama_barang 
                  FROM borrowings b 
                  JOIN users u ON b.user_id = u.id 
                  JOIN items i ON b.item_id = i.id 
                  WHERE b.status = 'pending' 
                  ORDER BY b.created_at DESC LIMIT 10";
$peminjaman_pending = mysqli_query($conn, $query_pending);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - sipintarFIK</title>
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
            gap: 6px;
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

       .sidebar {
            min-height: calc(100vh - 56px);
            background: white;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }
        .sidebar a {
            padding: 15px 20px;
            display: block;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
        }
        /* Hover */
        .sidebar a:hover {
            background: rgba(25, 135, 84, 0.12); /* success */
        }

        /* Active */
        .sidebar a.active {
            background: rgba(25, 135, 84, 0.18);
            color: #198754;
            font-weight: 600;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid var(--upnvj-gold);
        }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <img src="../assets/img/logo-upnvj.png" alt="Logo UPNVJ" class="logo-img">
                <span class="brand-text">sipintarFIK</span>
                <small class="text-white ms-1">Admin</small>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['nama']; ?>
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


    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="barang.php"><i class="fas fa-box"></i> Kelola Barang</a>
                    <a href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
                    <a href="peminjaman.php"><i class="fas fa-clipboard-list"></i> Peminjaman</a>
                    <a href="users.php"><i class="fas fa-users"></i> Users</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <h3><i class="fas fa-tachometer-alt"></i> Dashboard</h3>
                <hr>

                <!-- Stats -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $total_barang; ?></h3>
                            <small class="text-muted">Total Barang</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $total_user; ?></h3>
                            <small class="text-muted">Total User</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $total_peminjaman; ?></h3>
                            <small class="text-muted">Total Peminjaman</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <h3><?php echo $pending; ?></h3>
                            <small class="text-muted">Pending Approval</small>
                        </div>
                    </div>
                </div>

                <!-- Peminjaman Pending -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Peminjaman Menunggu Persetujuan</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($peminjaman_pending) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Peminjam</th>
                                            <th>Barang</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($peminjaman_pending)): ?>
                                        <tr>
                                            <td><?php echo $row['kode_peminjaman']; ?></td>
                                            <td><?php echo $row['nama']; ?></td>
                                            <td><?php echo $row['nama_barang']; ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td>
                                                <a href="peminjaman.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">Tidak ada peminjaman yang pending</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="row">
            <!-- Peminjaman Aktif -->
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
                                                    <span class="badge badge-pending">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge badge-approved">Disetujui</span>
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

            <!-- Menu Cepat -->
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
```
