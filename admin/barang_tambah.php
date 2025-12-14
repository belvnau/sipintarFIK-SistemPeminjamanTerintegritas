<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD']=='POST') {
    $kode = $_POST['kode_barang'];
    $nama = $_POST['nama_barang'];
    $kategori = $_POST['category_id'];
    $jumlah = $_POST['jumlah_total'];
    $deskripsi = $_POST['deskripsi'];

    mysqli_query($conn, "
        INSERT INTO items 
        (kode_barang,nama_barang,category_id,deskripsi,jumlah_total,jumlah_tersedia)
        VALUES ('$kode','$nama',$kategori,'$deskripsi',$jumlah,$jumlah)
    ");
    header('Location: barang.php?added=1');
    exit();
}

$categories = mysqli_query($conn,"SELECT * FROM categories ORDER BY nama_kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang</title>
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

        .btn-upnvj { background:var(--upnvj-green); color:#fff; }
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

<!-- SIDEBAR -->
<div class="col-md-2 p-0">
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="barang.php" class="active"><i class="fas fa-box"></i> Kelola Barang</a>
        <a href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
        <a href="peminjaman.php"><i class="fas fa-clipboard-list"></i> Peminjaman</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
    </div>
</div>

<!-- FORM -->
<div class="col-md-10 p-4">
    <h3><i class="fas fa-plus"></i> Tambah Barang</h3>
    <hr>

    <div class="card col-md-8">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label>Kode Barang</label>
                    <input type="text" name="kode_barang" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php while($c=mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $c['id']; ?>">
                                <?= $c['nama_kategori']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah_total" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control"></textarea>
                </div>

                <button class="btn btn-upnvj">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="barang.php" class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>

</div>
</div>

<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
