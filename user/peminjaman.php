<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

$user_id = $_SESSION['user_id'];

$query = "SELECT b.*, i.nama_barang, i.kode_barang 
          FROM borrowings b 
          JOIN items i ON b.item_id = i.id 
          WHERE b.user_id = $user_id 
          ORDER BY b.created_at DESC";
$peminjaman = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Saya - sipintarFIK</title>
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
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<!-- NAVBAR -->
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="barang.php">
                            <i class="fas fa-box"></i> Daftar Barang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="peminjaman.php">
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

<!-- CONTENT -->
<div class="container my-4">
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">
                <i class="fas fa-clipboard-list"></i> Riwayat Peminjaman
            </h4>

            <?php if (mysqli_num_rows($peminjaman) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-success text-center">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($peminjaman)): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><?= $row['kode_peminjaman']; ?></td>
                                <td>
                                    <strong><?= $row['nama_barang']; ?></strong><br>
                                    <small class="text-muted"><?= $row['kode_barang']; ?></small>
                                </td>
                                <td class="text-center"><?= $row['jumlah']; ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                                <td class="text-center">
                                    <?php
                                    $status = $row['status'];
                                    $badge = 'secondary';
                                    if ($status == 'pending') $badge = 'warning';
                                    elseif ($status == 'disetujui') $badge = 'success';
                                    elseif ($status == 'ditolak') $badge = 'danger';
                                    elseif ($status == 'dikembalikan') $badge = 'primary';
                                    ?>
                                    <span class="badge bg-<?= $badge; ?>">
                                        <?= ucfirst($status); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i>
                    Kamu belum memiliki data peminjaman.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
