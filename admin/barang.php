<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM items WHERE id = $id");
    header('Location: barang.php?deleted=1');
    exit();
}

$items = mysqli_query($conn, "SELECT i.*, c.nama_kategori FROM items i JOIN categories c ON i.category_id = c.id ORDER BY i.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang - sipintarFIK</title>
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
       .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .btn-upnvj { background: var(--upnvj-green); color: white; }
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
            <div class="col-md-2 p-0">
                <div class="sidebar">
                    <div class="sidebar">
                        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                        <a href="barang.php" class="active"><i class="fas fa-box"></i> Kelola Barang</a>
                        <a href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
                        <a href="peminjaman.php"><i class="fas fa-clipboard-list"></i> Peminjaman</a>
                        <a href="users.php"><i class="fas fa-users"></i> Users</a>
                    </div>
                </div>
            </div>
        

            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3><i class="fas fa-box"></i> Kelola Barang</h3>
                    <a href="barang_tambah.php" class="btn btn-upnvj"><i class="fas fa-plus"></i> Tambah Barang</a>
                </div>
                <hr>

                <?php if (isset($_GET['deleted'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        Barang berhasil dihapus!
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Total</th>
                                        <th>Tersedia</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($items)): ?>
                                    <tr>
                                        <td><?php echo $row['kode_barang']; ?></td>
                                        <td><?php echo $row['nama_barang']; ?></td>
                                        <td><?php echo $row['nama_kategori']; ?></td>
                                        <td><?php echo $row['jumlah_total']; ?></td>
                                        <td><?php echo $row['jumlah_tersedia']; ?></td>
                                        <td>
                                            <?php if ($row['status'] == 'tersedia'): ?>
                                                <span class="badge bg-success">Tersedia</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Tidak Tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="barang_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="barang.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Yakin hapus barang ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
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

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>