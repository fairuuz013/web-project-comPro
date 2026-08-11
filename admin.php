<?php
session_start();

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Koneksi Database
$host     = 'localhost';
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'my_database';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// Opsi Pilihan Dropdown
$list_kategori = ['Baju', 'Celana', 'Sepatu', 'Outer', 'Aksesoris'];
$list_gender   = ['Pria', 'Wanita', 'Unisex'];

// --- PROSES: TAMBAH DATA ---
if (isset($_POST['tambah'])) {
    $nama      = htmlspecialchars($_POST['nama_produk']);
    $kategori  = htmlspecialchars($_POST['kategori']);
    $gender    = htmlspecialchars($_POST['gender']);
    $harga     = (int)$_POST['harga'];
    $deskripsi = htmlspecialchars($_POST['deskripsi']);

    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'assets/uploads/' . $gambar);
    }

    $stmt = $conn->prepare("INSERT INTO products (nama_produk, kategori, gender, harga, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $nama, $kategori, $gender, $harga, $deskripsi, $gambar);

    if ($stmt->execute()) {
        $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Produk berhasil ditambahkan!'];
    } else {
        $_SESSION['alert'] = ['type' => 'danger', 'msg' => 'Gagal menambahkan produk!'];
    }
    header("Location: admin.php");
    exit;
}

// --- PROSES: EDIT DATA ---
if (isset($_POST['edit'])) {
    $id          = (int)$_POST['id'];
    $nama        = htmlspecialchars($_POST['nama_produk']);
    $kategori    = htmlspecialchars($_POST['kategori']);
    $gender      = htmlspecialchars($_POST['gender']);
    $harga       = (int)$_POST['harga'];
    $deskripsi   = htmlspecialchars($_POST['deskripsi']);
    $gambar_lama = $_POST['gambar_lama'];

    $gambar = $gambar_lama;
    if (!empty($_FILES['gambar']['name'])) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'assets/uploads/' . $gambar);

        if (!empty($gambar_lama) && file_exists('assets/uploads/' . $gambar_lama)) {
            unlink('assets/uploads/' . $gambar_lama);
        }
    }

    $stmt = $conn->prepare("UPDATE products SET nama_produk=?, kategori=?, gender=?, harga=?, deskripsi=?, gambar=? WHERE id=?");
    $stmt->bind_param("sssissi", $nama, $kategori, $gender, $harga, $deskripsi, $gambar, $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Produk berhasil diperbarui!'];
    } else {
        $_SESSION['alert'] = ['type' => 'danger', 'msg' => 'Gagal memperbarui produk!'];
    }
    header("Location: admin.php");
    exit;
}

// --- PROSES: HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    $stmt_select = $conn->prepare("SELECT gambar FROM products WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $res = $stmt_select->get_result();

    if ($row = $res->fetch_assoc()) {
        if (!empty($row['gambar']) && file_exists('assets/uploads/' . $row['gambar'])) {
            unlink('assets/uploads/' . $row['gambar']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Produk berhasil dihapus!'];
    } else {
        $_SESSION['alert'] = ['type' => 'danger', 'msg' => 'Gagal menghapus produk!'];
    }
    header("Location: admin.php");
    exit;
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ruuz Supply</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Dashboard Admin - Ruuz Supply</a>
        <div>
            <a href="company.php" class="btn btn-outline-light btn-sm me-2">Lihat Katalog</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">

    <!-- ALERT NOTIFIKASI -->
    <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-<?= $_SESSION['alert']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['alert']['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Kelola Produk</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah Produk</button>
    </div>

    <!-- TABEL PRODUK -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Gender</th>
                            <th>Harga</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($row['gambar']) && file_exists("assets/uploads/" . $row['gambar'])): ?>
                                            <img src="assets/uploads/<?= $row['gambar'] ?>" width="50" height="50" class="rounded object-fit-cover">
                                        <?php else: ?>
                                            <span class="text-muted small">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_produk']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= $row['gender'] == 'Pria' ? 'primary' : ($row['gender'] == 'Wanita' ? 'danger' : 'info') ?>">
                                            <?= htmlspecialchars($row['gender'] ?? 'Unisex') ?>
                                        </span>
                                    </td>
                                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">Edit</button>
                                        <a href="admin.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus</a>
                                    </td>
                                </tr>

                                <!-- MODAL EDIT DATA -->
                                <div class="modal fade" id="modalEdit<?= $row['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="admin.php" method="POST" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Edit Produk</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <input type="hidden" name="gambar_lama" value="<?= $row['gambar'] ?>">

                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Produk</label>
                                                        <input type="text" name="nama_produk" class="form-control" value="<?= htmlspecialchars($row['nama_produk']) ?>" required>
                                                    </div>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Kategori</label>
                                                        <select name="kategori" class="form-select" required>
                                                            <?php foreach ($list_kategori as $kat): ?>
                                                                <option value="<?= $kat ?>" <?= $row['kategori'] == $kat ? 'selected' : '' ?>><?= $kat ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Target Gender</label>
                                                        <select name="gender" class="form-select" required>
                                                            <?php foreach ($list_gender as $gnd): ?>
                                                                <option value="<?= $gnd ?>" <?= ($row['gender'] ?? '') == $gnd ? 'selected' : '' ?>><?= $gnd ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Harga (Rp)</label>
                                                        <input type="number" name="harga" class="form-control" value="<?= $row['harga'] ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Deskripsi</label>
                                                        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Ganti Gambar (Opsional)</label>
                                                        <input type="file" name="gambar" class="form-control" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="edit" class="btn btn-warning">Simpan Perubahan</button>
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

<!-- MODAL TAMBAH DATA -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="nama_produk" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="kategori" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <?php foreach ($list_kategori as $kat): ?>
                                <option value="<?= $kat ?>"><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Target Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Gender --</option>
                            <?php foreach ($list_gender as $gnd): ?>
                                <option value="<?= $gnd ?>"><?= $gnd ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Produk</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Tambah Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>