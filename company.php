<?php
session_start();

$host     = 'localhost';
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'my_database';

$no_wa = '62895332351824'; 

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
    <title>Ruuz Supply - Retail Store</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Tambahan Bootstrap Icons untuk Icon WhatsApp -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
        .navbar-brand-custom { font-weight: 800; letter-spacing: 1px; color: #212529; }
        .product-card { background: #ffffff; border: 1px solid #e9ecef; border-radius: 8px; overflow: hidden; transition: transform 0.2s ease; }
        .product-card:hover { transform: translateY(-3px); }
        .product-img-wrapper { height: 220px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; }
        .product-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
        .btn-whatsapp {
            background-color: #25D366;
            color: #ffffff;
            font-weight: 600;
            border: none;
        }
        .btn-whatsapp:hover {
            background-color: #1da851;
            color: #ffffff;
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
                <a href="profile.php" class="btn btn-outline-dark btn-sm fw-bold">&larr; Kembali ke Profil</a>
                
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
            <p class="text-muted small">Koleksi pakaian retail pilihan untuk menunjang gaya kasual dan formal Anda.</p>
        </div>

        <div class="row g-4 mb-5">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php 
                        // Menyusun Format Pesan WhatsApp
                        $harga_formatted = number_format($row['harga'], 0, ',', '.');
                        $pesan_wa = "Halo Ruuz Supply, saya mau pesan produk ini:\n\n";
                        $pesan_wa .= " *Nama Produk:* " . $row['nama_produk'] . "\n";
                        $pesan_wa .= " *Kategori:* " . $row['kategori'] . "\n";
                        $pesan_wa .= " *Harga:* Rp " . $harga_formatted . "\n\n";
                        $pesan_wa .= "Apakah stok masih tersedia?";
                        
                        $link_wa = "https://wa.me/" . $no_wa . "?text=" . urlencode($pesan_wa);
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="product-card h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="product-img-wrapper">
                                    <?php if (!empty($row['gambar']) && file_exists("assets/uploads/" . $row['gambar'])): ?>
                                        <img src="assets/uploads/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                                    <?php else: ?>
                                        <span class="text-muted fw-semibold">No Image</span>
                                    <?php endif; ?>
                                </div>
                                <div class="p-4">
                                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($row['kategori']) ?></span>
                                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['nama_produk']) ?></h5>
                                    <p class="text-muted small mb-3"><?= htmlspecialchars($row['deskripsi']) ?></p>
                                    <h6 class="fw-bold text-danger fs-5 mb-0">Rp <?= $harga_formatted ?></h6>
                                </div>
                            </div>
                            <!-- TOMBOL PESAN VIA WHATSAPP -->
                            <div class="p-4 pt-0">
                                <a href="<?= $link_wa ?>" target="_blank" class="btn btn-whatsapp w-100 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-whatsapp"></i> Beli via WhatsApp
                                </a>
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
                <a href="profile.php" class="text-muted small text-decoration-none me-3">Portofolio</a>
                <a href="login.php" class="text-muted small text-decoration-none me-3">Admin Panel</a>
                <a href="#" class="text-muted small text-decoration-none">Kembali ke Atas &uarr;</a>
            </div>
        </div>
    </div>
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>