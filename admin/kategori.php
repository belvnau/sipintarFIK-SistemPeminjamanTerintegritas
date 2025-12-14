<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

$kategori = mysqli_query($conn, "
    SELECT c.*, COUNT(i.id) AS total_barang
    FROM categories c
    LEFT JOIN items i ON c.id = i.category_id
    GROUP BY c.id
    ORDER BY c.nama_kategori ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kategori - sipintarFIK</title>
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
        <a href="kategori.php" class="active"><i class="fas fa-tags"></i> Kategori</a>
        <a href="peminjaman.php"><i class="fas fa-clipboard-list"></i> Peminjaman</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
    </div>
</div>

<!-- CONTENT -->
<div class="col-md-10 p-4">
    <h3><i class="fas fa-tags"></i> Data Kategori</h3>
    <hr>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Total Barang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no=1; while($row=mysqli_fetch_assoc($kategori)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['nama_kategori']; ?></td>
                        <td>
                            <span class="badge bg-success">
                                <?= $row['total_barang']; ?> barang
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#modal<?= $row['id']; ?>">
                                <i class="fas fa-eye"></i> Lihat Barang
                            </button>
                        </td>
                    </tr>

                    <!-- MODAL BARANG -->
                    <div class="modal fade" id="modal<?= $row['id']; ?>">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Barang - <?= $row['nama_kategori']; ?>
                                    </h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        <?php
                                        $barang = mysqli_query($conn,
                                            "SELECT nama_barang FROM items WHERE category_id={$row['id']}"
                                        );
                                        if(mysqli_num_rows($barang)==0):
                                        ?>
                                            <li class="list-group-item text-muted">
                                                Tidak ada barang
                                            </li>
                                        <?php else: while($b=mysqli_fetch_assoc($barang)): ?>
                                            <li class="list-group-item">
                                                <i class="fas fa-box"></i> <?= $b['nama_barang']; ?>
                                            </li>
                                        <?php endwhile; endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
