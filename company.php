<?php
session_start();

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'my_database';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruuz Supply - Retail Store & Catalog</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
        }

        .navbar-brand-custom {
            font-weight: 800;
            letter-spacing: 1px;
            color: #212529;
        }

        .product-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .product-card:hover {
            transform: translateY(-3px);
        }

        .product-img-wrapper {
            height: 220px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>

    <div class="main-content">
        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
            <div class="container">
                <a class="navbar-brand navbar-brand-custom text-uppercase fs-4" href="#">RUUZ SUPPLY</a>
                <div class="d-flex align-items-center gap-3">
                    <a href="profile.php" class="btn btn-outline-dark btn-sm fw-bold">&larr; Profil Company</a>

                    <?php if (isset($_SESSION['admin_logged_in'])): ?>
                        <a href="admin.php" class="btn btn-danger btn-sm fw-bold">Dashboard Admin &rarr;</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-danger btn-sm fw-bold">Login Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- CONTENT -->
        <div class="container mt-5">
            <div class="text-center mb-5">
                <span class="text-uppercase text-danger fw-bold small">Retail Collection</span>
                <h2 class="fw-bold text-dark mt-1">Pilihan Terbaik Kami</h2>
                <p class="text-muted small">Koleksi pakaian retail pilihan untuk menunjang gaya kasual dan formal Anda.
                </p>
            </div>

            <div class="row g-4 mb-5">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="product-card h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="product-img-wrapper">
                                        <?php if (!empty($row['gambar']) && file_exists("assets/uploads/" . $row['gambar'])): ?>
                                            <img src="assets/uploads/<?= $row['gambar'] ?>"
                                                alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                                        <?php else: ?>
                                            <span class="text-muted fw-semibold">No Image</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-4">
                                        <div class="mb-2">
                                            <span
                                                class="badge bg-secondary me-1"><?= htmlspecialchars($row['kategori']) ?></span>
                                            <span
                                                class="badge bg-<?= $row['gender'] == 'Pria' ? 'primary' : ($row['gender'] == 'Wanita' ? 'danger' : 'info') ?>">
                                                <?= htmlspecialchars($row['gender'] ?? 'Unisex') ?>
                                            </span>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['nama_produk']) ?></h5>
                                        <p class="text-muted small mb-3"><?= htmlspecialchars($row['deskripsi']) ?></p>
                                        <h6 class="fw-bold text-danger fs-5 mb-0">Rp
                                            <?= number_format($row['harga'], 0, ',', '.') ?></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Belum ada barang di katalog toko.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-white border-top py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center gy-3">
                <div class="col-md-6 text-center text-md-start">
                    <h6 class="fw-bold text-uppercase mb-1 tracking-wider">RUUZ SUPPLY</h6>
                    <p class="text-muted small mb-0">&copy; <?= date('Y') ?> Ruuz Supply. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="profile.php" class="text-muted small text-decoration-none me-3">Portofolio / Profil</a>
                    <a href="login.php" class="text-muted small text-decoration-none me-3">Admin Panel</a>
                    <a href="#" class="text-muted small text-decoration-none">Kembali ke Atas &uarr;</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>