<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

/* ===== HANDLE APPROVE ===== */
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE borrowings SET status='disetujui' WHERE id=$id");
    header("Location: peminjaman.php?approved=1");
    exit();
}

/* ===== HANDLE REJECT ===== */
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $b = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM borrowings WHERE id=$id"));
    mysqli_query($conn, "UPDATE items 
                         SET jumlah_tersedia = jumlah_tersedia + {$b['jumlah']} 
                         WHERE id={$b['item_id']}");
    mysqli_query($conn, "UPDATE borrowings SET status='ditolak' WHERE id=$id");
    header("Location: peminjaman.php?rejected=1");
    exit();
}

/* ===== DATA ===== */
$peminjaman = mysqli_query($conn, "
    SELECT b.*, u.nama, u.nim, i.nama_barang
    FROM borrowings b
    JOIN users u ON b.user_id = u.id
    JOIN items i ON b.item_id = i.id
    ORDER BY b.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Peminjaman - sipintarFIK</title>
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
        .card { border:none; box-shadow:0 2px 8px rgba(0,0,0,.1); }
    </style>
</head>

<body>

<!-- NAVBAR -->
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

<!-- SIDEBAR -->
<div class="col-md-2 p-0">
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="barang.php"><i class="fas fa-box"></i> Kelola Barang</a>
        <a href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
        <a href="peminjaman.php" class="active"><i class="fas fa-clipboard-list"></i> Peminjaman</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
    </div>
</div>

<!-- CONTENT -->
<div class="col-md-10 p-4">
    <h4><i class="fas fa-clipboard-list"></i> Kelola Peminjaman</h4>
    <hr>

    <?php if (isset($_GET['approved'])): ?>
        <div class="alert alert-success">Peminjaman disetujui</div>
    <?php endif; ?>
    <?php if (isset($_GET['rejected'])): ?>
        <div class="alert alert-danger">Peminjaman ditolak</div>
    <?php endif; ?>

    <div class="card">
    <div class="card-body">
    <div class="table-responsive">

    <table class="table table-hover align-middle">
        <thead>
        <tr>
            <th>Kode</th>
            <th>Peminjam</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($peminjaman)): ?>
        <tr>
            <td><?= $row['kode_peminjaman']; ?></td>
            <td><?= $row['nama']; ?><br><small><?= $row['nim']; ?></small></td>
            <td><?= $row['nama_barang']; ?></td>
            <td><?= $row['jumlah']; ?></td>
            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
            <td>
                <?php
                $badge = [
                    'pending'=>'warning',
                    'disetujui'=>'success',
                    'ditolak'=>'danger'
                ];
                ?>
                <span class="badge bg-<?= $badge[$row['status']] ?>">
                    <?= ucfirst($row['status']); ?>
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-info"
                        data-bs-toggle="modal"
                        data-bs-target="#detail<?= $row['id']; ?>">
                    <i class="fas fa-eye"></i> Detail
                </button>

                <?php if ($row['status']=='pending'): ?>
                    <a href="?approve=<?= $row['id']; ?>" class="btn btn-sm btn-success"
                       onclick="return confirm('Setujui peminjaman?')">
                        <i class="fas fa-check"></i>
                    </a>
                    <a href="?reject=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Tolak peminjaman?')">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    </div>
    </div>
    </div>
</div>
</div>
</div>

<?php
mysqli_data_seek($peminjaman, 0);
while ($row = mysqli_fetch_assoc($peminjaman)):
?>
<!-- MODAL DETAIL (DI LUAR TABLE) -->
<div class="modal fade" id="detail<?= $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Peminjaman</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr><th>Kode</th><td><?= $row['kode_peminjaman']; ?></td></tr>
                    <tr><th>Peminjam</th><td><?= $row['nama']; ?> (<?= $row['nim']; ?>)</td></tr>
                    <tr><th>Barang</th><td><?= $row['nama_barang']; ?></td></tr>
                    <tr><th>Jumlah</th><td><?= $row['jumlah']; ?></td></tr>
                    <tr><th>Tujuan</th><td><?= $row['tujuan']; ?></td></tr>
                    <tr><th>Tgl Pinjam</th><td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td></tr>
                    <tr><th>Tgl Kembali</th><td><?= date('d/m/Y', strtotime($row['tanggal_kembali'])); ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
