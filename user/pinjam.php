<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT i.*, c.nama_kategori FROM items i JOIN categories c ON i.category_id = c.id WHERE i.id = $item_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: barang.php');
    exit();
}

$item = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $tanggal_kembali = $_POST['tanggal_kembali'];
    $tujuan = mysqli_real_escape_string($conn, $_POST['tujuan']);
    
    $kode = 'PJM-' . date('Ymd') . '-' . rand(1000, 9999);
    
    if ($jumlah > $item['jumlah_tersedia']) {
        $error = "Jumlah melebihi stok tersedia!";
    } else {
        $insert = "INSERT INTO borrowings (kode_peminjaman, user_id, item_id, jumlah, tanggal_pinjam, tanggal_kembali, tujuan, status) 
                   VALUES ('$kode', $user_id, $item_id, $jumlah, '$tanggal_pinjam', '$tanggal_kembali', '$tujuan', 'pending')";
        
        if (mysqli_query($conn, $insert)) {
            $new_stock = $item['jumlah_tersedia'] - $jumlah;
            mysqli_query($conn, "UPDATE items SET jumlah_tersedia = $new_stock WHERE id = $item_id");
            
            header('Location: peminjaman.php?success=1');
            exit();
        } else {
            $error = "Gagal mengajukan peminjaman!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman - sipintarFIK</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Form Peminjaman Barang</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="alert alert-info">
                            <h6><strong><?php echo $item['nama_barang']; ?></strong></h6>
                            <p class="mb-0">Kategori: <?php echo $item['nama_kategori']; ?> | Tersedia: <?php echo $item['jumlah_tersedia']; ?> unit</p>
                        </div>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Jumlah Pinjam <span class="text-danger">*</span></label>
                                <input type="number" name="jumlah" class="form-control" min="1" max="<?php echo $item['jumlah_tersedia']; ?>" required>
                                <small class="text-muted">Maksimal: <?php echo $item['jumlah_tersedia']; ?> unit</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_pinjam" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_kembali" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tujuan Peminjaman <span class="text-danger">*</span></label>
                                <textarea name="tujuan" class="form-control" rows="4" placeholder="Jelaskan untuk apa barang ini dipinjam..." required></textarea>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="barang.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
                                <button type="submit" class="btn btn-upnvj"><i class="fas fa-paper-plane"></i> Ajukan Peminjaman</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>