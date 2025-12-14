<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header('Location: ../index.php');
    exit();
}
include '../config/database.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

$query = "SELECT i.*, c.nama_kategori 
          FROM items i 
          JOIN categories c ON i.category_id = c.id 
          WHERE i.status = 'tersedia'";

if ($search) {
    $query .= " AND (i.nama_barang LIKE '%$search%' OR i.kode_barang LIKE '%$search%')";
}
if ($category) {
    $query .= " AND i.category_id = '$category'";
}
$query .= " ORDER BY i.nama_barang ASC";

$items = mysqli_query($conn, $query);
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY nama_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang - sipintarFIK</title>
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
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .btn-upnvj { background: var(--upnvj-green); color: white; }
        .btn-upnvj:hover { background: #2d5a3d; color: white; }
        .item-img {
            width: 100%;
            height: 200px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #adb5bd;
        }
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
                        <a class="nav-link active" href="barang.php">
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
        <h3><i class="fas fa-box"></i> Daftar Barang Tersedia</h3>
        <hr>

        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="<?php echo $search; ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['nama_kategori']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-upnvj w-100"><i class="fas fa-search"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-4">
            <?php if (mysqli_num_rows($items) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($items)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="item-img">
                            <?php if ($item['gambar']): ?>
                                <img src="../uploads/barang/<?php echo $item['gambar']; ?>" 
                                    class="w-100 h-100" 
                                    style="object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-image"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $item['nama_barang']; ?></h5>
                            <p class="card-text text-muted small"><?php echo $item['nama_kategori']; ?></p>
                            <p class="card-text"><?php echo substr($item['deskripsi'], 0, 100); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-success">Tersedia: <?php echo $item['jumlah_tersedia']; ?>/<?php echo $item['jumlah_total']; ?></span>
                                <a href="pinjam.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-upnvj">
                                    <i class="fas fa-hand-holding"></i> Pinjam
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Tidak ada barang yang tersedia
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>