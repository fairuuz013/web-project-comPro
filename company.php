<?php
// Konfigurasi Database Sesuai Log Docker
$host     = 'db'; 
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'my_database';

mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// Direktori Upload Gambar
$target_dir = "assets/uploads/";

// PROCESS: TAMBAH PRODUK (CREATE)
if (isset($_POST['add_product'])) {
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $kategori  = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga     = (int)$_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $gambar    = "";

    // Logika Upload Gambar
    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
        $filename   = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $filename;
        }
    }

    $conn->query("INSERT INTO products (nama_produk, kategori, harga, deskripsi, gambar) VALUES ('$nama', '$kategori', '$harga', '$deskripsi', '$gambar')");
    header("Location: company.php");
    exit();
}

// PROCESS: EDIT PRODUK (UPDATE)
if (isset($_POST['edit_product'])) {
    $id        = (int)$_POST['id'];
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $kategori  = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga     = (int)$_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];

    $gambar = $gambar_lama;

    // Jika user mengunggah gambar baru
    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
        $filename    = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $filename;
            // Hapus gambar lama jika ada
            if (!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)) {
                unlink($target_dir . $gambar_lama);
            }
        }
    }

    $conn->query("UPDATE products SET nama_produk='$nama', kategori='$kategori', harga='$harga', deskripsi='$deskripsi', gambar='$gambar' WHERE id=$id");
    header("Location: company.php");
    exit();
}


// PROCESS: HAPUS PRODUK (DELETE)

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Ambil info gambar sebelum dihapus dari DB
    $res = $conn->query("SELECT gambar FROM products WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['gambar']) && file_exists($target_dir . $row['gambar'])) {
            unlink($target_dir . $row['gambar']);
        }
    }

    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: company.php");
    exit();
}

// Fetch Semua Produk (READ)
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruuz Supply - Retail Store</title>
    <!-- CSS Bootstrap Lokal -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
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
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .product-img-wrapper {
            height: 220px;
            width: 100%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-img-placeholder {
            color: #6c757d;
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand navbar-brand-custom text-uppercase fs-4" href="#">RUUZ SUPPLY</a>
        <div class="d-flex align-items-center gap-3">
            <a href="profile.php" class="btn btn-outline-dark btn-sm fw-bold">&larr; Kembali ke Profil</a>
            <button class="btn btn-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addModal">
                + Tambah Barang
            </button>
        </div>
    </div>
</nav>

<!-- HERO / TITLE SECTION -->
<div class="container mt-5">
    <div class="text-center mb-5">
        <span class="text-uppercase text-danger fw-bold small tracking-wider">Retail Collection</span>
        <h2 class="fw-bold text-dark mt-1">Pilihan Terbaik Kami</h2>
        <p class="text-muted small">Koleksi pakaian retail pilihan untuk menunjang gaya kasual dan formal Anda.</p>
    </div>

    <!-- PRODUCT GRID (READ) -->
    <div class="row g-4 mb-5">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="product-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Render Gambar Produk -->
                            <div class="product-img-wrapper">
                                <?php if (!empty($row['gambar']) && file_exists("assets/uploads/" . $row['gambar'])): ?>
                                    <img src="assets/uploads/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                                <?php else: ?>
                                    <span class="product-img-placeholder">No Image</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-4">
                                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($row['kategori']) ?></span>
                                <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['nama_produk']) ?></h5>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($row['deskripsi']) ?></p>
                                <h6 class="fw-bold text-danger fs-5 mb-0">
                                    Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                </h6>
                            </div>
                        </div>
                        
                        <!-- ACTION BUTTONS -->
                        <div class="p-4 pt-0 d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm w-100 fw-semibold" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal<?= $row['id'] ?>">
                                Edit
                            </button>
                            <a href="company.php?delete=<?= $row['id'] ?>" 
                               class="btn btn-outline-danger btn-sm w-100 fw-semibold"
                               onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                Hapus
                            </a>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT BARANG -->
                <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="company.php" method="POST" enctype="multipart/form-data">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Barang</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="gambar_lama" value="<?= $row['gambar'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Nama Produk</label>
                                        <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($row['nama_produk']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Kategori</label>
                                        <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($row['kategori']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Harga (Rp)</label>
                                        <input type="number" name="harga" class="form-control" value="<?= $row['harga'] ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Deskripsi</label>
                                        <textarea name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Ganti Foto Produk (Opsional)</label>
                                        <input type="file" name="gambar" class="form-control" accept="image/*">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="edit_product" class="btn btn-danger fw-bold">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada barang di toko. Klik **+ Tambah Barang** untuk menambahkan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL TAMBAH BARANG (CREATE) -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="company.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Barang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Kemeja Flanel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Baju / Celana / Outer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" placeholder="150000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi singkat produk..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Foto Produk</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_product" class="btn btn-danger fw-bold">Tambah Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Bootstrap 5 -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>