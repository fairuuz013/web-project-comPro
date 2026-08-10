<?php
session_start();

// Proteksi Halaman Admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Konfigurasi Database
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'my_database';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

$target_dir = "assets/uploads/";

// PROCESS: TAMBAH PRODUK (CREATE)
if (isset($_POST['add_product'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga = (int) $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $gambar = "";

    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
        $filename = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $filename;
        }
    }

    $conn->query("INSERT INTO products (nama_produk, kategori, harga, deskripsi, gambar) VALUES ('$nama', '$kategori', '$harga', '$deskripsi', '$gambar')");
    header("Location: admin.php");
    exit();
}

// PROCESS: EDIT PRODUK (UPDATE)
if (isset($_POST['edit_product'])) {
    $id = (int) $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga = (int) $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];
    $gambar = $gambar_lama;

    if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['name'] != "") {
        $filename = time() . '_' . basename($_FILES['gambar']['name']);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            $gambar = $filename;
            if (!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)) {
                unlink($target_dir . $gambar_lama);
            }
        }
    }

    $conn->query("UPDATE products SET nama_produk='$nama', kategori='$kategori', harga='$harga', deskripsi='$deskripsi', gambar='$gambar' WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// PROCESS: HAPUS PRODUK (DELETE)
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $res = $conn->query("SELECT gambar FROM products WHERE id=$id");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['gambar']) && file_exists($target_dir . $row['gambar'])) {
            unlink($target_dir . $row['gambar']);
        }
    }
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: admin.php");
    exit();
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Ruuz Supply</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold text-uppercase" href="#">RUUZ SUPPLY - ADMIN PANEL</a>
            <div class="d-flex align-items-center gap-3">
                <a href="company.php" class="btn btn-outline-light btn-sm fw-semibold" target="_blank">Lihat Web Publik
                    &rarr;</a>
                <a href="logout.php" class="btn btn-danger btn-sm fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Manajemen Produk Retail</h3>
                <p class="text-muted small mb-0">Kelola katalog produk, harga, dan gambar toko.</p>
            </div>
            <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addModal">+ Tambah
                Barang</button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php if (!empty($row['gambar']) && file_exists("assets/uploads/" . $row['gambar'])): ?>
                                                <img src="assets/uploads/<?= $row['gambar'] ?>" class="table-img" alt="Foto">
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?= htmlspecialchars($row['nama_produk']) ?></td>
                                        <td><span
                                                class="badge bg-light text-dark border"><?= htmlspecialchars($row['kategori']) ?></span>
                                        </td>
                                        <td class="fw-bold text-danger">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                        <td class="small text-muted" style="max-width: 250px;">
                                            <?= htmlspecialchars($row['deskripsi']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-secondary btn-sm me-1 fw-semibold"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal<?= $row['id'] ?>">Edit</button>
                                            <a href="admin.php?delete=<?= $row['id'] ?>"
                                                class="btn btn-outline-danger btn-sm fw-semibold"
                                                onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                        </td>
                                    </tr>

                                    <!-- MODAL EDIT BARANG -->
                                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="admin.php" method="POST" enctype="multipart/form-data">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Edit Barang</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                        <input type="hidden" name="gambar_lama" value="<?= $row['gambar'] ?>">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Nama Produk</label>
                                                            <input type="text" name="nama_produk" class="form-control"
                                                                value="<?= htmlspecialchars($row['nama_produk']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Kategori</label>
                                                            <input type="text" name="kategori" class="form-control"
                                                                value="<?= htmlspecialchars($row['kategori']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Harga (Rp)</label>
                                                            <input type="number" name="harga" class="form-control"
                                                                value="<?= $row['harga'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Deskripsi</label>
                                                            <textarea name="deskripsi" class="form-control" rows="3"
                                                                required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-bold">Ganti Foto Produk
                                                                (Opsional)</label>
                                                            <input type="file" name="gambar" class="form-control"
                                                                accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" name="edit_product"
                                                            class="btn btn-danger fw-bold">Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data produk.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH BARANG -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="admin.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Tambah Barang Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-control"
                                placeholder="Contoh: Kemeja Flanel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kategori</label>
                            <input type="text" name="kategori" class="form-control" placeholder="Contoh: Baju / Celana"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" placeholder="150000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi produk..."
                                required></textarea>
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

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>