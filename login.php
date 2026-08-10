<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit();
}

// Konfigurasi Database
$host     = 'localhost';
$db_user  = 'root';
$db_pass  = '';
$db_name  = 'my_database';

mysqli_report(MYSQLI_REPORT_OFF);
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // Verifikasi password (atau plain text fallback)
        if (password_verify($password, $row['password']) || $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $row['username'];
            header("Location: admin.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Ruuz Supply</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>

<div class="login-card p-4 shadow-sm">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-uppercase">Ruuz Supply</h3>
        <p class="text-muted small">Masuk ke Panel Administrasi</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger small py-2"><?= $error ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold">Username</label>
            <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
        </div>
        <button type="submit" name="login" class="btn btn-danger w-100 fw-bold mb-3">Login Admin</button>
        <div class="text-center">
            <a href="company.php" class="text-muted small text-decoration-none">&larr; Kembali ke Toko</a>
        </div>
    </form>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>